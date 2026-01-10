<?php

namespace Red\Model\Dao\Hierarchy;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Pes\Database\Handler\HandlerInterface;

use Red\Model\Dao\Hierarchy\HookedMenuItemActorInterface;

use Pes\Text\FriendlyUrl;

/**
 * Třída pro editaci nested set hierarchie.
 *
 * Podle tutoriálu na https://www.phpro.org/tutorials/Managing-Hierarchical-Data-with-PHP-and-MySQL.html - pozor jsou tam chyby
 * V tutoriálu jsou přepracované sql skripty, které zveřejnil http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/ - a od té doby je všichni kopíruji.
 */
class HierarchyAggregateEditDao extends HierarchyAggregateReadonlyDao implements HierarchyAggregateEditDaoInterface {

    protected $hookedActor;

    /**
     * Jako parametr příjímá objekt HookedContentActionsInterface, jeho metody jsou volány při operacích vkládání nebo mazání položek v hierarchii.
     * Tento objekt tak umožňuje "pověsit" na operace vkládání a mazání položek v hierarchii další operace, s databázovými tabulkami. Tyto další
     * operace probíhají uvnitř transakce, ve které se vkládají nebo mažou položky hierarchie a typicky tak umožňují přidání nebo mazání identifikátorů
     * položek hierarchie (uid) použitých jako cizí klíče v dalších tabulkách.
     *
     * @param HookedContentActionsInterface $hookedActions
     */
    public function registerHookedActor(HookedMenuItemActorInterface $hookedActions) {
        $this->hookedActor = $hookedActions;
    }

    public function getHookedActor(): HookedMenuItemActorInterface {
        return $this->hookedActor;
    }

#### pomocné čtecí metody ###################################################

    /**
     * Pomocná metoda - čte data jen z db tabulky pro nested set (hierarchy), nikoli agregátní.
     * Vrací řádek s položkami: depth, uid, left_node, right_node, parent_uid
     *
     * @param type $uid
     * @return array|null
     */
    public function getParentNodeHelper($uid) {
        $sql =
            "SELECT
                (COUNT(grand_parent.uid) - 1) AS depth,
                parent.uid, parent.left_node, parent.right_node, parent.parent_uid
                FROM
                $this->nestedSetTableName AS node
                CROSS JOIN
                $this->nestedSetTableName AS parent
                ON parent.left_node<node.left_node AND parent.right_node>node.right_node
                CROSS JOIN
                $this->nestedSetTableName AS grand_parent ON parent.left_node BETWEEN grand_parent.left_node AND grand_parent.right_node
                WHERE node.uid = :uid
                GROUP BY parent.uid
                ORDER BY parent.left_node DESC
            LIMIT 1"
                ;
        $stmt = $this->getPreparedStatement($sql);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() == 1 ? $stmt->fetch() : NULL;
    }

#### editační metody ########################################################

    /**
     * Vloží nový kořenový uzel nested setu do prázdné tabulky. Vytvoří uzel s parametry left node = 1, right node = 2 a uid rodiče = NULL.
     *
     * @return integer Automaticky generované uid vloženého uzlu
     * @throws LogicException Pokud tabulka pro ukládání nested setu není prázdná.
     */
    public function newNestedSet() {
        $stmt = $this->getPreparedStatement("SELECT uid FROM $this->nestedSetTableName");
        $stmt->execute();
        if($stmt->rowCount()) {
            throw new \LogicException("Tabulka pro uložení nested set není prázná. Tabulka '$this->nestedSetTableName' má {$stmt->rowCount()} řádek.");
        }

        return $this->insertNode(1, 2, NULL);
    }

    /**
     * Vloží nový uzel. Neprovádí žádné kontroly. Vrací nově vygenerovaný uid.
     * Je možno zadat duplicitní levé i pravé uzly, libovolně nesmyslné uzly a pod. Dovoluje zadat uid rodiče, ale jde o nepovinný parametr.
     *
     * @param integer $leftNode
     * @param integer $rightNode
     * @param type $parentNodeUid Je možné zadat uid rodiče. To se nepoužívá pro funkce nested set (hierarchy), ale může být použito pro rekonstrukci hierarchie v případě havárie (rozpadu) struktury nested setu.
     * @return integer Automaticky generované uid vloženého uzlu
     * @throws Exception
     *
     */
    public function insertNode($leftNode, $rightNode, $parentNodeUid=NULL) {
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $uid = $this->createNewUidWithinTransaction($dbhTransact);

            /*** insert the new node ***/
            $stmt = $this->getPreparedStatement(
                    "INSERT INTO $this->nestedSetTableName(uid, left_node, right_node, parent_uid) VALUES (:uid, :left_node, :right_node, :parent_uid)");
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':left_node', $leftNode);
            $stmt->bindParam(':right_node', $rightNode);
            $stmt->bindParam(':parent_uid', $parentNodeUid);
            $stmt->execute();
            if (isset($this->hookedActor)) {
                $this->hookedActor->add($dbhTransact, $parentNodeUid, $uid);
            }
            /*** commit the transaction ***/
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $uid;
    }

    /**
     * Generuje uid unikátní v rámci tabulky. Metodu lze použít jen v průběhu již spuštěné transakce.
     *
     * Generuje uid pomocí PHP funkce uniqid(), ale ověří, že vygenerované uid skutečně není v tabulce dosud použito, pokud je, generuje nové uid.
     * Funkce uniquid (bez prefixua s bez vyšší entropue) generuje řetězec dlouhý 13 znaků,
     *
     * Aby byla zaručena unikátnost uid v rámci jedné tabulky, je nutné, aby čtení tabulky při zjišťování existence uid a následný zápis nového
     * zázamu proběhly se zamčenou tabulkou. Tato metoda používá příkaz "SELECT uid FROM hierarchy_table WHERE uid = :uid LOCK IN SHARE MODE", který zamkne přečtené záznamy až
     * do okamžiku ukončení transakce. Proto lze tuto metodu použít jen v průběhu již spuštěné transakce. Volání této metody mimo spuštěnou transakci vyvolá výjimku.
     *
     * @param HandlerInterface $dbhTransact
     * @return type
     * @throws \LogicException Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.
     */
    private function createNewUidWithinTransaction(HandlerInterface $dbhTransact) {
        if ($dbhTransact->inTransaction()) {
            do {
                $uid = uniqid();
                $stmt =$this->getPreparedStatement(
                        "SELECT uid
                        FROM $this->nestedSetTableName
                        WHERE uid = :uid LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam(':uid', $uid);
                $stmt->execute();   // vrací uid pokud již v tabulce existuje
            } while ($stmt->rowCount());    // pokud bylo uid nalezeno, vykoná další cyklus
            return $uid;
        } else {
            throw new \LogicException('Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.');
        }

    }

    /**
     * Přidá uzel jako potomka zadaného uzlu. Vrací automaticky generované uid vloženého uzlu.
     * @param int $parentNodeUid
     * @return int Automaticky generované uid vloženého uzlu
     * @throws Exception
     */
    public function addChildNode($parentNodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement(
                    "SELECT @myLeft := left_node
                    FROM $this->nestedSetTableName
                    WHERE uid=:node_uid FOR UPDATE");
            $stmt->bindParam(':node_uid', $parentNodeUid);
            $stmt->execute();
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET right_node = right_node + 2
                    WHERE right_node > @myLeft");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET left_node = left_node + 2
                    WHERE left_node > @myLeft");
            $uid = $this->createNewUidWithinTransaction($dbhTransact);
            $stmt = $this->getPreparedStatement(
                    "INSERT INTO $this->nestedSetTableName(uid, left_node, right_node, parent_uid) VALUES(:uid, @myLeft + 1, @myLeft + 2, :parent_uid)");
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':parent_uid', $parentNodeUid);
            $stmt->execute();
            if (isset($this->hookedActor)) {
                $this->hookedActor->add($dbhTransact, $parentNodeUid, $uid);
            }
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $uid;
    }

    /**
     * Přidá uzel jako potomka zadaného uzlu. Vrací automaticky generované uid vloženého uzlu.
     * @param int $parentNodeUid
     * @return int Automaticky generované uid vloženého uzlu
     * @throws Exception
     */
    public function addChildNodeAsLast($parentNodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement(
                    "SELECT @myRight := right_node
                    FROM $this->nestedSetTableName
                    WHERE uid=:node_uid FOR UPDATE");
            $stmt->bindParam(':node_uid', $parentNodeUid);
            $stmt->execute();
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET right_node = right_node + 2
                    WHERE right_node >= @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET left_node = left_node + 2
                    WHERE left_node > @myRight");
            $uid = $this->createNewUidWithinTransaction($dbhTransact);
            $stmt = $this->getPreparedStatement(
                    "INSERT INTO $this->nestedSetTableName(uid, left_node, right_node, parent_uid) VALUES(:uid, @myRight, @myRight + 1, :parent_uid)");
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':parent_uid', $parentNodeUid);
            $stmt->execute();
            if (isset($this->hookedActor)) {
                $this->hookedActor->add($dbhTransact, $parentNodeUid, $uid);
            }
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $uid;
    }


    /**
     * Přidá uzel jako sourozence zadaného uzlu, t.j. na stejnou úroveň. Vrací automaticky generované uid vloženého uzlu.
     *
     * @param int $leftNodeUid
     * @return int Automaticky generované uid vloženého uzlu
     * @throws Exception
     */
    public function addNode($leftNodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement(
                    "SELECT @myRight := right_node, @parentUid := parent_uid
                    FROM $this->nestedSetTableName
                    WHERE uid = :left_node_uid");
            $stmt->bindParam(':left_node_uid', $leftNodeUid);
            $stmt->execute();
            /*** increment the nodes by two ***/
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET right_node = right_node + 2
                    WHERE right_node > @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET left_node = left_node + 2
                    WHERE left_node > @myRight");
            /*** insert the new node ***/
            $uid = $this->createNewUidWithinTransaction($dbhTransact);
            $stmt = $this->getPreparedStatement(
                    "INSERT INTO $this->nestedSetTableName(uid, left_node, right_node, parent_uid) VALUES (:uid, @myRight + 1, @myRight + 2, @parentUid)");  // přidá doprava za zadaný uzel - t.j. bezprostředně pod vybranou položku při svislém zobrazení
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            if (isset($this->hookedActor)) {
                $this->hookedActor->add($dbhTransact, $leftNodeUid, $uid);
            }
            /*** commit the transaction ***/
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $uid;
    }


    /**
     * Smaže uzel, pokud je to list, t.j. uzel na konci větve grafu uzlů.
     *
     * @param string $nodeUid
     */
    public function deleteLeafNode($nodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement(
                    "SELECT @myLeft := left_node, @myRight := right_node, @myWidth := right_node - left_node + 1, parent_uid
                    FROM $this->nestedSetTableName WHERE uid = :node_uid AND right_node - left_node = 1");
            $stmt->bindParam(':node_uid', $nodeUid, \PDO::PARAM_STR);
            $stmt->execute();
            $nodeRow = $stmt->fetch();
            $stmt = $this->getPreparedStatement(
                    "SELECT uid FROM $this->nestedSetTableName
                    WHERE left_node BETWEEN @myLeft AND @myRight");
            $stmt->execute();
            $uidsToDelete = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);  //první sloupec
            // nejdřív je třeba smazat položky, které obsahují klíče z $this->nestedSetTableName jako cizí klíče - jinak dojde k chybě Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails
            // maže se menu_item (ten má ON DELETE RESTRICT) a asset a menu_item_aset (menu_item_aset je vazební tabulka a nesmazaly by se assets po mastavení ON DELETE CASCADE na menu_item_aset)
            // article, paper (včetně sections), multipage, static se mažou samy mají ON DELETE CASCADE na menu_item_fk
            // menu root se také maže sám, má ON DELETE CASCADE na menu_item_fk (ne ma hierarchy - to asi není dokonalé)
            // asi bylo možné nastavit menu_item ON DELETE CASCADE na menu_item_uid_fk, pak by zůstal je problém s vazební tabulkou assetů (??)
            if (isset($this->hookedActor)) {
                $this->hookedActor->delete($dbhTransact, $uidsToDelete);
            }
            $dbhTransact->exec(
                    "DELETE FROM $this->nestedSetTableName
                    WHERE left_node BETWEEN @myLeft AND @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName SET right_node = right_node - @myWidth
                    WHERE right_node > @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName SET left_node = left_node - @myWidth
                    WHERE left_node > @myRight");

            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    /**
     * Smaže uzel a všechny jeho potomky.
     *
     * @param string $nodeUid
     */
    public function deleteSubTree($nodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement(
                    "SELECT @myLeft := left_node, @myRight := right_node, @myWidth := right_node - left_node + 1, parent_uid
                    FROM $this->nestedSetTableName WHERE uid = :node_uid FOR UPDATE");
            $stmt->bindParam(':node_uid', $nodeUid, \PDO::PARAM_STR);
            $stmt->execute();
            $nodeRow = $stmt->fetch();
            $stmt = $this->getPreparedStatement(
                    "SELECT uid FROM $this->nestedSetTableName
                    WHERE left_node BETWEEN @myLeft AND @myRight");
            $stmt->execute();
            $uidsToDelete = $stmt->fetchAll(\PDO::FETCH_NUM);
            if (isset($this->hookedActor)) {
                $this->hookedActor->delete($dbhTransact, $uidsToDelete);
            }
            $dbhTransact->exec(
                    "DELETE FROM $this->nestedSetTableName
                    WHERE left_node BETWEEN @myLeft AND @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName SET right_node = right_node - @myWidth
                    WHERE right_node > @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName SET left_node = left_node - @myWidth
                    WHERE left_node > @myRight");
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    /**
     * Přesune podstrom (zdrojový uzel a všechny jeho potomky) jako dítě cílového uzlu.
     * Defaultně deaktivuje všechny položky menu příslušné k přesunutým uzlům.
     *
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu.
     *
     * @param string $sourceUid uid zdrojového uzlu
     * @param string $targetUid uid cílového uzlu
     * @param type $targetUid
     * @param bool $deactivate
     * @return void
     * @throws Exception
     */
    public function moveSubTreeAsChild($sourceUid, $targetUid, $deactivate=true): void {
        $dbhTransact = $this->dbHandler;
        try {

            // parametry
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement("SET @sourceId := :source_uid");
            $stmt->bindParam(':source_uid', $sourceUid);
            $stmt->execute();
            $stmt = $this->getPreparedStatement("SET @targetId := :target_uid");
            $stmt->bindParam(':target_uid', $targetUid);
            $stmt->execute();

            // data zdrojového uzlu
            $dbhTransact->exec("SELECT left_node, right_node, right_node-left_node+1 INTO @source_left_node, @source_right_node, @source_width
                FROM $this->nestedSetTableName WHERE uid = @sourceId");

            // vyřazení zdrojovéjo podstromu z nested set
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = 0-left_node, right_node = 0-right_node
                WHERE left_node BETWEEN @source_left_node AND @source_right_node");
            // zpátky:
            // UPDATE $this->nestedSetTableName SET left_node = 0-left_node, right_node = 0-right_node WHERE left_node<0;
            //SELECT @sourceId, @targetId, @source_left_node, @source_right_node, @source_width ;

            // odstraň prostor zbylý po vyřazeném podstromu
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node-@source_width WHERE right_node > @source_right_node");
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node-@source_width WHERE left_node > @source_right_node");

            // data cílového uzlu (načtou se až po odstranění prostoru zbylého po přesunovaném stromu)
            $dbhTransact->exec("SELECT left_node INTO @target_left_node
                FROM $this->nestedSetTableName WHERE uid = @targetId");

            // vytvoř cílový volný prostor
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node+@source_width WHERE right_node >= @target_left_node");
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node+@source_width WHERE left_node > @target_left_node");

            // deaktivace položek /menu item) - vybírá se jen podle uid -> deaktivuji všechny jazykové verze
            if ($deactivate) {
                // MySQL syntaxe!
                $dbhTransact->exec("UPDATE hierarchy AS nested_set INNER JOIN menu_item AS items SET items.active = 0
                    WHERE
                     nested_set.left_node < 0 AND nested_set.uid=items.uid_fk");
            }
            // vrácení podstromu do nested set
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET
                left_node = 0 - left_node - (@source_left_node - @target_left_node - 1),
                right_node = 0 - right_node - (@source_left_node - @target_left_node - 1)
                WHERE left_node < 0");
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    public function moveSubTreeAsSiebling($sourceUid, $targetUid, $deactivate=true): void {
        $dbhTransact = $this->dbHandler;
        try {

            // parametry
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement("SET @sourceId := :source_uid");
            $stmt->bindParam(':source_uid', $sourceUid);
            $stmt->execute();
            $stmt = $this->getPreparedStatement("SET @targetId := :target_uid");
            $stmt->bindParam(':target_uid', $targetUid);
            $stmt->execute();

            // data zdrojového uzlu
            $dbhTransact->exec("SELECT left_node, right_node, right_node-left_node+1 INTO @source_left_node, @source_right_node, @source_width
                FROM $this->nestedSetTableName WHERE uid = @sourceId");

            // vyřazení zdrojovéjo podstromu z nested set
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = 0-left_node, right_node = 0-right_node
                WHERE left_node BETWEEN @source_left_node AND @source_right_node");
            // zpátky:
            // UPDATE $this->nestedSetTableName SET left_node = 0-left_node, right_node = 0-right_node WHERE left_node<0;
            //SELECT @sourceId, @targetId, @source_left_node, @source_right_node, @source_width ;

            // odstraň prostor zbylý po vyřazeném podstromu
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node-@source_width WHERE right_node > @source_right_node");
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node-@source_width WHERE left_node > @source_right_node");

            // data cílového uzlu (načtou se až po odstranění prostoru zbylého po přesunovaném stromu)
            $dbhTransact->exec("SELECT right_node INTO @target_right_node
                FROM $this->nestedSetTableName WHERE uid = @targetId");

            // vytvoř cílový volný prostor "vpravo" od cíle
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node+@source_width WHERE right_node > @target_right_node");
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node+@source_width WHERE left_node > @target_right_node");

            // deaktivace položek /menu item) - vybírá se jen podle uid -> deaktivuji všechny jazykové verze
            if ($deactivate) {
                // MySQL syntaxe!
                $dbhTransact->exec("UPDATE hierarchy AS nested_set INNER JOIN menu_item AS items SET items.active = 0
                    WHERE
                     nested_set.left_node < 0 AND nested_set.uid=items.uid_fk");
            }
            // vrácení podstromu do nested set
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET
                left_node = 0 - left_node - (@source_left_node - @target_right_node - 1),
                right_node = 0 - right_node - (@source_left_node - @target_right_node - 1)
                WHERE left_node < 0");
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    /**
     * Zkopíruje podstrom (zdrojový uzel a všechny jeho potomky) jako dítě cílového uzlu. Zkopíroje také položky menu (menu item).
     * Defaultně zkopírované položky nastaví jako neaktivní (nepublikované).
     *
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu.
     *
     * Vrací pole kde indexy jsou uid zdrojových položek menu_item a hodnoty uid cílových položek menu_item.
     * 
     * @param type $sourceUid
     * @param type $targetUid
     * @param type $deactivate
     * @return array
     * @throws Exception
     */
    public function copySubTreeAsChild($sourceUid, $targetUid, $deactivate=true): array {
        $dbhTransact = $this->dbHandler;
        try {
            // parametry
            $dbhTransact->beginTransaction();

            $prepareNodesDataStmt = $this->getPreparedStatement("SET @sourceId := :source_uid");
            $prepareNodesDataStmt->bindParam(':source_uid', $sourceUid);
            $prepareNodesDataStmt->execute();
            $prepareNodesDataStmt = $this->getPreparedStatement("SET @targetId := :target_uid");
            $prepareNodesDataStmt->bindParam(':target_uid', $targetUid);
            $prepareNodesDataStmt->execute();

            // data zdrojového uzlu
            $dbhTransact->exec("SELECT left_node, right_node, right_node-left_node+1 INTO @source_left_node, @source_right_node, @source_width
                FROM $this->nestedSetTableName WHERE uid = @sourceId");

            // data cílového uzlu
            $dbhTransact->exec("SELECT left_node INTO @target_left_node
                FROM $this->nestedSetTableName WHERE uid = @targetId");

            // cílová data ze zdrojových dat (před vytvořením cílového prostoru - přídáním cílového prostoru se mohou zdrojová data posunout doprava)
            // data obsahují: zdrojové uid, cílový left node, cílový right node 
            $prepareNodesDataStmt = $this->getPreparedStatement("
                    SELECT uid, left_node - (@source_left_node - @target_left_node - 1) AS left_node, right_node - (@source_left_node - @target_left_node - 1) AS right_node
                    FROM $this->nestedSetTableName WHERE left_node BETWEEN @source_left_node AND @source_right_node
                ");
            $prepareNodesDataStmt->execute();
            $preparedNodeData = $prepareNodesDataStmt->fetchAll(\PDO::FETCH_ASSOC);

            // vytvoř cílový volný prostor
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node+@source_width WHERE right_node >= @target_left_node");
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node+@source_width WHERE left_node > @target_left_node");

            // kopíruj obsahy
            $transform = $this->copySourceContentIntoTarget($dbhTransact, $preparedNodeData, $deactivate);
            
            $this->replaceInternalLinks($transform);

            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $transform;
    }

    /**
     * Zkopíruje podstrom (zdrojový uzel a všechny jeho potomky) jako sourozence cílového uzlu. Vloží podstrom vpravo od cílového uzlu. Zkopíroje také položky menu (menu item).
     * Defaultně zkopírované položky nastaví jako neaktivní (nepublikované).
     *
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu.
     *
     * Vrací pole kde indexy jsou uid zdrojových položek menu_item a hodnoty uid cílových položek menu_item.
     * 
     * @param type $sourceUid
     * @param type $targetUid
     * @return array
     * @throws Exception
     */
    public function copySubTreeAsSiebling($sourceUid, $targetUid, $deactivate=true): array {
         $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            
            // parametry
            $stmt = $this->getPreparedStatement("SET @sourceId := :source_uid");
            $stmt->bindParam(':source_uid', $sourceUid);
            $stmt->execute();
            $stmt = $this->getPreparedStatement("SET @targetId := :target_uid");
            $stmt->bindParam(':target_uid', $targetUid);
            $stmt->execute();

            // data zdrojového uzlu
            $dbhTransact->exec("SELECT left_node, right_node, right_node-left_node+1 INTO @source_left_node, @source_right_node, @source_width
                FROM $this->nestedSetTableName WHERE uid = @sourceId");

            // data cílového uzlu
            $dbhTransact->exec("SELECT right_node INTO @target_right_node
                FROM $this->nestedSetTableName WHERE uid = @targetId");

            // cílová data ze zdrojových dat (před vytvořením cílového prostoru - přídáním cílového prostoru se mohou zdrojová data posunout doprava)
            // data obsahují: zdrojové uid, cílový left node, cílový right node 
            $prepareNodesDataStmt = $this->getPreparedStatement("
                    SELECT uid, left_node - (@source_left_node - @target_right_node - 1) AS left_node, right_node - (@source_left_node - @target_right_node - 1) AS right_node
                    FROM $this->nestedSetTableName WHERE left_node BETWEEN @source_left_node AND @source_right_node
                ");
            $prepareNodesDataStmt->execute();
            $preparedNodeData = $prepareNodesDataStmt->fetchAll(\PDO::FETCH_ASSOC);

            // vytvoř cílový volný prostor
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET right_node = right_node+@source_width WHERE right_node > @target_right_node");
            $dbhTransact->exec("UPDATE $this->nestedSetTableName SET left_node = left_node+@source_width WHERE left_node > @target_right_node");

            // kopíruj obsahy - metoda kopíruje položky menu_item
            $transform = $this->copySourceContentIntoTarget($dbhTransact, $preparedNodeData, $deactivate);

            $this->replaceInternalLinks($transform);
            
            $dbhTransact->commit();
            
            
            
            
            
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $transform;
    }

    /**
     * 
     * @param type $transform
     */
    private function replaceInternalLinks( $transform ){  
        $selectSourceItemsStmt = $this->getPreparedStatement("
                    SELECT id FROM 
                        $this->itemTableName 
                        WHERE
                        ($this->itemTableName.uid_fk=:source_uid AND
                         $this->itemTableName.lang_code_fk = 'cs')
        ");
        $selectArticleStm = $this->getPreparedStatement("
                    SELECT id, article
                        FROM article
                        WHERE
                        article.menu_item_id_fk=:source_id
        ");
        $updadteArticlerStm = $this->getPreparedStatement("       
                    UPDATE article
                        SET article = :article                           
                        WHERE id = :id                   
        ");
        $selectPaperStm= $this->getPreparedStatement("                
                    SELECT id, perex, headline
                        FROM  paper
                        WHERE
                        paper.menu_item_id_fk = :source_id
        ");
        
        $updadtePaperStm = $this->getPreparedStatement("       
                    UPDATE paper
                        SET perex = :perex, 
                            headline = :headline
                        WHERE id = :id                   
        ");
        $selectPaperSectionStm = $this->getPreparedStatement("                
                    SELECT id, content
                        FROM  paper_section
                        WHERE
                        paper_id_fk=:source_id
        ");
        $updadtePaperSectionStm = $this->getPreparedStatement("       
                    UPDATE paper_section
                        SET content = :content                             
                        WHERE id=:id                   
        ");
        
        
        
        foreach ($transform as $uidStrankyKterouHledam => $uidStrankyKDEHledam) {
            //zjistit id pro zadane uid z tabulky menu_item
            
            $this->bindParams($selectSourceItemsStmt, ['source_uid'=> $uidStrankyKDEHledam ]);
            $selectSourceItemsStmt->execute();
            $sourceItems = $selectSourceItemsStmt->fetch(\PDO::FETCH_ASSOC);   //je tam dycky jeden  
            //  precist texty z tabulek podle//menu_item.id
            $menuItemIdKDEHledam = $sourceItems['id'];   
            
            //article           
            $this->bindParams($selectArticleStm, ['source_id'=>$menuItemIdKDEHledam]);
            $selectArticleStm->execute();
            $sourceArticle = $selectArticleStm->fetch(\PDO::FETCH_ASSOC);    //je tam dycky jeden                ;
            if  ($sourceArticle['article']) {
                $articleArticleNew = str_replace( array_keys($transform), array_values ($transform), $sourceArticle['article'], $countArticle);   
                if ($countArticle) { //update jen pri zmene                    
                    $this->bindParams($updadteArticlerStm, ['article'=>$articleArticleNew, 'id'=>$sourceArticle['id'] ] );
                    $updadteArticlerStm -> execute();
                }
            }           
            
            //paper                      
            $this->bindParams($selectPaperStm, ['source_id'=>$menuItemIdKDEHledam] );
            $selectPaperStm -> execute();
            $sourcePaper =  $selectPaperStm->fetch(\PDO::FETCH_ASSOC);   //je tam dycky jeden             
                if  ($sourcePaper['perex']) { 
                    $paperPerexNew = str_replace( array_keys($transform), array_values ($transform), $sourcePaper['perex'], $countPerex);                       
                } else {
                    $countPerex = 0; $paperPerexNew = '';
                }
                if  ($sourcePaper['headline']) { 
                    $paperHeadlineNew = str_replace( array_keys($transform), array_values ($transform), $sourcePaper['headline'], $countHeadline);   
                } else {
                    $countHeadline = 0; $paperHeadlineNew = '';
                }              
                if ($countHeadline OR $countPerex) {  //update jen pri zmene                 
                    $this->bindParams($updadtePaperStm, ['perex'=>$paperPerexNew, 'headline'=>$paperHeadlineNew, 
                                                         'id'=>$sourcePaper['id']] );
                    $updadtePaperStm -> execute();
                }
        
                //paper_section               
                $this->bindParams($selectPaperSectionStm, ['source_id'=> $sourcePaper['id'] ] );
                $selectPaperSectionStm -> execute();
                $sourcePaperSections =  $selectPaperSectionStm->fetchAll(\PDO::FETCH_ASSOC); //array s vice nez jednim prvkem     
                if ($selectPaperSectionStm->rowCount()>0) {
                    foreach ($sourcePaperSections as $sekce) { 
                        if ($sekce['content']) {
                            $contentNew = str_replace( array_keys($transform), array_values ($transform), $sekce['content'], $countContent);    
                        } else {
                            $countContent = 0; $contentNew = '';                             
                        }                        
                        if ($countContent) {  //update jen pri zmene                           
                            $this->bindParams($updadtePaperSectionStm, ['content'=>$contentNew, 'id'=>$sekce['id'] ] ); 
                            $updadtePaperSectionStm -> execute();   
                        }
                    }

                }                    
                
            //multipage-nic                          
        }          
    }
    
    
    
    
    
    
    
    /**
     * Metoda kopíruje položky menu_item podle pole nodů zadaného jako parametr. Kopíruje položky všech jazykových verzí.
     * Defaultně zkopírované položky nastaví jako neaktivní (nepublikované).
     *
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu. To musí být splněno ve všech jazykových verzích.
     *
     * Vrací pole kde indexy jsou uid zdrojových položek menu_item a hodnoty uid cílových položek menu_item. 
     * To lze využít pro nahrazení menu_item uid zdrojových položek v obsahu stránek (typicky v href) za uid cílových stránek.
     *  
     * @param type $dbhTransact transact handler
     * @param array $preparedNodeData pole dat připravených cílových uzlů nested set (hierarchy nodes)
     *  - data obsahují: zdrojové uid, cílový left node, cílový right node 
     * @param bool $deactivate
     * @return array Pole klíče menu_item uid k nahrazení v obsahu stránek.
     */
    private function copySourceContentIntoTarget($dbhTransact, $preparedNodeData, $deactivate=true) {
        $insertToTargetStmt = $this->getPreparedStatement("INSERT INTO $this->nestedSetTableName (uid, left_node, right_node)  VALUES (:uid, :left_node, :right_node)");
        // select jen podle uid_fk -> vybere všechny jazykové verze
        $selectSourceItemsStmt = $this->getPreparedStatement("
                SELECT lang_code_fk, api_module_fk, api_generator_fk, id, `list`, `order`, title, prettyuri, active, auto_generated
                    FROM
                    $this->itemTableName
                    WHERE
                    $this->itemTableName.uid_fk=:source_uid
            ");
        $insertTargetItemStmt = $this->getPreparedStatement("
                INSERT INTO menu_item (lang_code_fk, uid_fk, api_module_fk, api_generator_fk, `list`, `order`, title, prettyuri, active, auto_generated)
                VALUES (:lang_code_fk, :uid_fk, :api_module_fk, :api_generator_fk, :list, :order, :title, :prettyuri, :active, :auto_generated)
            ");

        $preparedCopyArticle = $this->getPreparedStatement("
                INSERT INTO article (menu_item_id_fk, article, template, editor, updated)
                    SELECT :new_menu_item_id, article, template, editor, updated
                    FROM
                    article
                    WHERE
                    article.menu_item_id_fk=:source_menu_item_id
            ");
        $preparedCopyPaper = $this->getPreparedStatement("
                INSERT INTO paper (menu_item_id_fk, headline, perex, template, keywords, editor, updated)
                    SELECT :new_menu_item_id, headline, perex, template, keywords, editor, updated
                    FROM
                    paper
                    WHERE
                    paper.menu_item_id_fk=:source_menu_item_id
            ");
        $preparedCopySections = $this->getPreparedStatement("
                INSERT INTO paper_section (paper_id_fk, list, content, template_name, template, active, priority, show_time, hide_time, event_start_time, event_end_time, editor, updated)
                    SELECT :new_paper_id_fk, list, content, template_name, template, active, priority, show_time, hide_time, event_start_time, event_end_time, editor, updated
                    FROM
                        paper_section
                        WHERE
                        paper_id_fk=
                        (
                        SELECT id FROM paper WHERE menu_item_id_fk=:source_menu_item_id
                        )
            ");
        $preparedCopyMultipage = $this->getPreparedStatement("
                INSERT INTO multipage (menu_item_id_fk, template, editor, updated)
                    SELECT :new_menu_item_id, template, editor, updated
                    FROM
                    multipage
                    WHERE
                    multipage.menu_item_id_fk=:source_menu_item_id
            ");
        $preparedCopyStatic = $this->getPreparedStatement("
                INSERT INTO static (menu_item_id_fk, path, template, creator, updated) 
                    SELECT :new_menu_item_id, path, template, creator, updated 
                    FROM 
                    static 
                    WHERE 
                    static.menu_item_id_fk=:source_menu_item_id
                    ");
        $preparedCopyAssets = $this->getPreparedStatement("
                INSERT INTO menu_item_asset (menu_item_id_fk, asset_id_fk) 
                    SELECT :new_menu_item_id, asset_id_fk
                    FROM
                    menu_item_asset
                    WHERE 
                    menu_item_asset.menu_item_id_fk=:source_menu_item_id
                    ");

        $transform = [];
        foreach ($preparedNodeData as $nodeData) {
            $sourceUid = $nodeData['uid'];  // uid zdrojového node
            $targetUid = $this->createNewUidWithinTransaction($dbhTransact);
            $transform[$sourceUid] = $targetUid;
            $nodeData['uid'] = $targetUid;  
            $this->bindParams($insertToTargetStmt, $nodeData);  // vloží node s nově vygenerovsným uid s připraveným left_node, right_node
            $insertToTargetStmt->execute();
            $this->bindParams($selectSourceItemsStmt, ['source_uid'=>$sourceUid]);
            $selectSourceItemsStmt->execute();
            $sourceItems = $selectSourceItemsStmt->fetchAll(\PDO::FETCH_ASSOC);  // items pro všechny jazykové verze
            foreach ($sourceItems as $sourceItem) {
                // a) tabulka menu_item: unique key a) kombinace lang_code a uid, b) prettyUri
                // při volání metody dao get c parametrem check duplicities vzniká chyba při duplicitě lang_code a list
                // b) aktivní menu_item pod neaktivní - vyvolá chybné načtení stromu položek menu v needitačním režimu - ve stromu jsou "díry"
                // a MOŽNÁ "rekurzivní" renderování selže
                // ->
                // a) uid - nový uid, list - prázdný (jinak by vznikly duplicity při výběru podle jazyka a listu)
                // prettyUri - složit s novým uid
                // active - pokud parametr deactivate je true nastaví vždy 0, zkopírované položky jsou vždy všechny neaktivní - pro případ selhávání rekurzivního renderování stromu menu
                //        - pokud deactivate je false - zkopíruje active ze zdrojové položky
                // sloupec prettyUri má 200chars. Limit titulku nastavuji na 200. (totéž HierarchyAggregateEditDao)
                $prefix = $sourceItem['lang_code_fk'].$targetUid.'-';
                $prettyUri = $this->hookedActor->genaratePrettyUri($sourceItem['title'], $prefix);
                $this->bindParams($insertTargetItemStmt, [
                    'lang_code_fk'=>$sourceItem['lang_code_fk'], 'uid_fk'=>$targetUid, 
                    'api_module_fk'=>$sourceItem['api_module_fk'], 'api_generator_fk'=>$sourceItem['api_generator_fk'],
                    'list'=>($sourceItem['list'] ? 'copy_'.$sourceItem['list'] : ''), 
                    'order'=>$sourceItem['order'], 'title'=>$sourceItem['title'],
                    'prettyuri'=>$prettyUri,             
                    'active'=> ($deactivate ? 0 : $sourceItem['active']),
                    'auto_generated'=>$sourceItem['auto_generated']]);
                $insertTargetItemStmt->execute();
                $lastMenuItemId = $dbhTransact->lastInsertId();

                $this->bindParams($preparedCopyArticle, ['new_menu_item_id'=>$lastMenuItemId, 'source_menu_item_id'=>$sourceItem['id']]);
                $articleCount = $preparedCopyArticle->execute();
                $this->bindParams($preparedCopyPaper, ['new_menu_item_id'=>$lastMenuItemId, 'source_menu_item_id'=>$sourceItem['id']]);
                $paperCount = $preparedCopyPaper->execute();
                $newPaperId = $dbhTransact->lastInsertId();
                $this->bindParams($preparedCopySections, ['new_paper_id_fk'=>$newPaperId, 'source_menu_item_id'=>$sourceItem['id']]);
                $sectionCount = $preparedCopySections->execute();
                $this->bindParams($preparedCopyMultipage, ['new_menu_item_id'=>$lastMenuItemId, 'source_menu_item_id'=>$sourceItem['id']]);
                $multipageCount = $preparedCopyMultipage->execute();
                $this->bindParams($preparedCopyStatic, ['new_menu_item_id'=>$lastMenuItemId, 'source_menu_item_id'=>$sourceItem['id']]);
                $staticCount = $preparedCopyStatic->execute();  
                $this->bindParams($preparedCopyAssets, ['new_menu_item_id'=>$lastMenuItemId, 'source_menu_item_id'=>$sourceItem['id']]);
                $assetCount = $preparedCopyAssets->execute();                
            }
        }
        return $transform;
    }

    /**
     * Smaže uzel a jeho potomky posune na jeho místo.
     *
     * @param type $nodeUid
     * @return void
     * @throws Exception
     */
    public function replaceNodeWithChild($nodeUid): void {
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $this->getPreparedStatement(
                    "SELECT @myLeft := left_node, @myRight := right_node, @myWidth := right_node - left_node + 1, parent_uid
                    FROM $this->nestedSetTableName
                    WHERE uid = :node_uid");
            $stmt->bindParam(':node_uid', $nodeUid);
            $stmt->execute();
            $nodeRow = $stmt->fetch();
            $stmt = $this->getPreparedStatement(
                    "SELECT uid FROM $this->nestedSetTableName
                    WHERE left_node = @myLeft");
            $stmt->execute();
            $uidsToDelete = $stmt->fetchAll(\PDO::FETCH_NUM);  //jeden
            if (isset($this->hookedActor)) {
                $this->hookedActor->delete($dbhTransact, $uidsToDelete);
            }
            $dbhTransact->exec(
                    "DELETE FROM $this->nestedSetTableName
                    WHERE left_node = @myLeft");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET right_node = right_node - 1, left_node = left_node - 1
                    WHERE left_node BETWEEN @myLeft AND @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET right_node = right_node - 2
                    WHERE right_node > @myRight");
            $dbhTransact->exec(
                    "UPDATE $this->nestedSetTableName
                    SET left_node = left_node - 2
                    WHERE left_node > @myRight");

            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    public function convetAdjacencyListToNestedSet() {

    }


//    public function convertAdjacencyListToNestedSet() {
//
//        $sql = <<<SQL
//        -- Tree holds the adjacency model
//        CREATE TABLE Tree
//        (emp CHAR(10) NOT NULL,
//         boss CHAR(10));
//
//        INSERT INTO Tree
//        SELECT emp, boss FROM Personnel;
//
//        -- Stack starts empty, will holds the nested set model
//        CREATE TABLE Stack
//        (stack_top INTEGER NOT NULL,
//         emp CHAR(10) NOT NULL,
//         lft INTEGER,
//         rgt INTEGER);
//
//        BEGIN ATOMIC
//        DECLARE counter INTEGER;
//        DECLARE max_counter INTEGER;
//        DECLARE current_top INTEGER;
//
//        SET counter = 2;
//        SET max_counter = 2 * (SELECT COUNT(*) FROM Tree);
//        SET current_top = 1;
//
//        INSERT INTO Stack
//        SELECT 1, emp, 1, NULL
//         FROM Tree
//         WHERE boss IS NULL;
//
//        DELETE FROM Tree
//         WHERE boss IS NULL;
//
//        WHILE counter <= (max_counter - 2)
//        LOOP IF EXISTS (SELECT *
//         FROM Stack AS S1, Tree AS T1
//         WHERE S1.emp = T1.boss
//         AND S1.stack_top = current_top)
//         THEN
//         BEGIN -- push when top has subordinates, set lft value
//         INSERT INTO Stack
//         SELECT (current_top + 1), MIN(T1.emp), counter, NULL
//         FROM Stack AS S1, Tree AS T1
//         WHERE S1.emp = T1.boss
//         AND S1.stack_top = current_top;
//
//         DELETE FROM Tree
//         WHERE emp = (SELECT emp
//         FROM Stack
//         WHERE stack_top = current_top + 1);
//
//         SET counter = counter + 1;
//         SET current_top = current_top + 1;
//         END
//         ELSE
//         BEGIN -- pop the stack and set rgt value
//         UPDATE Stack
//         SET rgt = counter,
//         stack_top = -stack_top -- pops the stack
//         WHERE stack_top = current_top
//         SET counter = counter + 1;
//         SET current_top = current_top - 1;
//         END IF;
//         END LOOP;
//        END;
//SQL;
//
//        set_time_limit(20);
//
//        $dbhTransact = $this->dbHandlerFactory->get();
//        try {
//            $dbhTransact->beginTransaction();
//            $dbhTransact->exec($sql);
//
//            $dbhTransact->commit();
//        } catch(Exception $e) {
//            $dbhTransact->rollBack();
//            throw new Exception($e);
//        }
//
//}

//    public function convertNestedSetToAdjacencyList() {
//
//        set_time_limit(20);
//
//        $dbhTransact = $this->dbHandlerFactory->get();
//        try {
//            $dbhTransact->beginTransaction();
//            $dbhTransact->exec("DROP TABLE IF EXISTS `adjacencylist`");
//            $dbhTransact->exec("CREATE TABLE `adjacencylist` (
//                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
//                `name` varchar(45) NOT NULL,
//                `parent` bigint(20) DEFAULT NULL,
//                PRIMARY KEY (`id`),
//                UNIQUE KEY `id` (`id`)
//                ) ENGINE=InnoDB AUTO_INCREMENT=16296 DEFAULT CHARSET=utf8;");
//            $dbhTransact->exec("INSERT INTO AdjacencyList
//                SELECT A.id AS id, A.name AS name, B.id AS parent
//                FROM $this->hierarchyTableName AS A LEFT OUTER JOIN $this->hierarchyTableName AS B
//                  ON B.left_node = (SELECT MAX(C.left_node)
//                             FROM $this->hierarchyTableName AS C
//                             WHERE A.left_node > C.left_node
//                               AND A.left_node < C.right_node)");
//
//            $dbhTransact->commit();
//        } catch(Exception $e) {
//            $dbhTransact->rollBack();
//            throw new Exception($e);
//        }
//}
    /**
     *
     * @Delete parent node and all its children move up
     *
     * @access public
     *
     * @param string $parentNodeId
     *
     */


}


