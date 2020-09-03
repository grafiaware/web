<?php

namespace Model\Dao\Hierarchy;

use Model\Dao\DaoAbstract;

use Pes\Database\Handler\HandlerInterface;
use Model\Context\ContextFactoryInterface;
use Model\Dao\Hierarchy\HookedMenuItemActorInterface;

/**
 * Třída pro editaci nested set hierarchie.
 *
 * Podle tutoriálu na https://www.phpro.org/tutorials/Managing-Hierarchical-Data-with-PHP-and-MySQL.html - pozor jsou tam chyby
 * V tutoriálu jsou přepracované sql skripty, které zveřejnil http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/ - a od té doby je všichni kopíruji.
 */
class NodeEditDao extends DaoAbstract implements NodeEditDaoInterface {

    protected $nestedSetTableName;

    protected $hookedActor;

    /**
     *
     * @param HandlerInterface $handler
     * @param strimg $nestedSetTableName Jméno tabulky obsahující nested set hierarchii položek. Používá se pro editaci hierarchie.
     * @param ContextFactoryInterface $contextFactory
     */
    public function __construct(HandlerInterface $handler, $nestedSetTableName, ContextFactoryInterface $contextFactory=null) {
        parent::__construct($handler, $contextFactory);
        $this->nestedSetTableName = $nestedSetTableName;
    }

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
     * `uid`, `left_node`, `right_node`, `parent_uid`, `lang_code_fk`AS lang_code, `uid_fk`, `type_fk`, `id`, `list`, `order`, `title`, `active`,`auto_generated`
     *
     * @param type $uid
     * @return array
     */
    public function getNode($uid) {
        $stmt = $this->dbHandler->prepare(
            "SELECT *
            FROM $this->nestedSetTableName
            WHERE uid = :uid");
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->rowCount() == 1 ? $stmt->fetch(\PDO::FETCH_ASSOC) : NULL;
    }

    public function getNodeByTitle($title) {
        $stmt = $this->dbHandler->prepare(
            "SELECT *
            FROM $this->viewName
            WHERE title = :title");
        $stmt->bindParam(':title', $title);
        $stmt->execute();
        return $stmt->rowCount() == 1 ? $stmt->fetch(\PDO::FETCH_ASSOC) : NULL;
    }

#### editační metody ########################################################

    /**
     * Vloží nový kořenový uzel nested setu do prázdné tabulky. Vytvoří uzel s parametry left node = 1, right node = 2 a uid rodiče = NULL.
     *
     * @return integer Automaticky generované uid vloženého uzlu
     * @throws LogicException Pokud tabulka pro ukládání nested setu není prázdná.
     */
    public function newNestedSet() {
        $dbh = $this->dbHandler;
        $stmt = $dbh->prepare("SELECT uid FROM $this->nestedSetTableName");
        $stmt->execute();
        if($stmt->rowCount()) {
            throw new \LogicException("Tabulka pro uložení nested et není prázná. Tabulka '$this->nestedSetTableName' má {$stmt->rowCount()} řádek.");
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
            $uid = $this->getNewUidWithinTransaction($dbhTransact);

            /*** insert the new node ***/
            $stmt = $dbhTransact->prepare(
                    "INSERT INTO $this->nestedSetTableName(uid, left_node, right_node, parent_uid) VALUES (:uid, :left_node, :right_node, :parent_uid)");
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':left_node', $leftNode);
            $stmt->bindParam(':right_node', $rightNode);
            $stmt->bindParam(':parent_uid', $parentNodeUid);
            $stmt->execute();
            if (isset($this->hookedActor)) {
                $this->hookedActor->add($dbhTransact, [$uid]);
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
     *
     * Aby byla zaručena unikátnost uid v rámci jedné tabulky, je nutné, aby čtení tabulky při zjišťování existence uid a následný zápis nového
     * zázamu proběhly se zamčenou tabulkou. Tato metoda používá příkaz "SELECT uid FROM hierarchy_table WHERE uid = :uid LOCK IN SHARE MODE", který zamkne přečtené záznamy až
     * do okamžiku ukončení transakce. Proto lze tuto metodu použít jen v průběhu již spuštěné transakce. Volání této metody mimo spuštěnou transakci vyvolá výjimku.
     *
     * @param HandlerInterface $dbhTransact
     * @return type
     * @throws \LogicException Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.
     */
    private function getNewUidWithinTransaction(HandlerInterface $dbhTransact) {
        if ($dbhTransact->inTransaction()) {
            do {
                $uid = uniqid();
                $stmt = $dbhTransact->prepare(
                        "SELECT uid
                        FROM $this->nestedSetTableName
                        WHERE uid = :uid LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam(':uid', $uid);
                $stmt->execute();
            } while ($stmt->rowCount());
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
            $stmt = $dbhTransact->prepare(
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
            $uid = $this->getNewUidWithinTransaction($dbhTransact);
            $stmt = $dbhTransact->prepare(
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
            $stmt = $dbhTransact->prepare(
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
            $uid = $this->getNewUidWithinTransaction($dbhTransact);
            $stmt = $dbhTransact->prepare(
                    "INSERT INTO $this->nestedSetTableName(uid, left_node, right_node, parent_uid) VALUES(:uid, @myRight, @myRight + 1, :parent_uid)");
            $stmt->bindParam(':uid', $uid);
            $stmt->bindParam(':parent_uid', $parentNodeUid);
            $stmt->execute();
            if (isset($this->hookedActor)) {
                $this->hookedActor->add($dbhTransact, [$uid]);
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
            $stmt = $dbhTransact->prepare(
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
            $uid = $this->getNewUidWithinTransaction($dbhTransact);
            $stmt = $dbhTransact->prepare(
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
     * Smaže uzel, pokud je to list, t.j. uzel na konci větve grafu uzlů. Vrací uid rodiče smazaného uzlu pro účely navigace.
     *
     * @param string $nodeUid
     * @return string parent_uid
     */
    public function deleteLeafNode($nodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $dbhTransact->prepare(
                    "SELECT @myLeft := left_node, @myRight := right_node, @myWidth := right_node - left_node + 1, parent_uid
                    FROM $this->nestedSetTableName WHERE uid = :node_uid AND right_node - left_node = 1");
            $stmt->bindParam(':node_uid', $nodeUid, \PDO::PARAM_STR);
            $stmt->execute();
            $nodeRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt = $dbhTransact->prepare(
                    "SELECT uid FROM $this->nestedSetTableName
                    WHERE left_node BETWEEN @myLeft AND @myRight");
            $stmt->execute();
            $uidsToDelete = $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);  //první sloupec
            // nejdřív je třeba smazat položky, které obsahují klíče z $this->nestedSetTableName jako cizí klíče - jinak dojde k chybě Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails
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
            return $nodeRow['parent_uid'];

        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    /**
     * Smaže uzel a všechny jeho potomky. Vrací uid rodiče zadaného uzlu pro účely navigace.
     *
     * @param string $nodeUid
     * @return string parent_uid
     */
    public function deleteSubTree($nodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $dbhTransact->prepare(
                    "SELECT @myLeft := left_node, @myRight := right_node, @myWidth := right_node - left_node + 1, parent_uid
                    FROM $this->nestedSetTableName WHERE uid = :node_uid FOR UPDATE");
            $stmt->bindParam(':node_uid', $nodeUid, \PDO::PARAM_STR);
            $stmt->execute();
            $nodeRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt = $dbhTransact->prepare(
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
            return $nodeRow['parent_uid'];

        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    /**
     * Přesune podstrom (zdrojový uzel a všechny jeho potomky) jako dítě cílového uzlu.
     *
     * @param string $sourceUid uid zdrojového uzlu
     * @param string $targetUid uid cílového uzlu
     * @throws Exception
     */
    public function moveSubTree($sourceUid, $targetUid): void {
        $dbhTransact = $this->dbHandler;
        try {

            // parametry
            $dbhTransact->beginTransaction();
            $stmt = $dbhTransact->prepare("SET @sourceId := :source_uid");
            $stmt->bindParam(':source_uid', $sourceUid);
            $stmt->execute();
            $stmt = $dbhTransact->prepare("SET @targetId := :target_uid");
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
            //
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

    /**
     * Smaže uzel a jeho potomky posune na jeho místo.
     */
    public function replaceNodeWithChild($nodeUid){
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $stmt = $dbhTransact->prepare(
                    "SELECT @myLeft := left_node, @myRight := right_node, @myWidth := right_node - left_node + 1, parent_uid
                    FROM $this->nestedSetTableName
                    WHERE uid = :node_uid");
            $stmt->bindParam(':node_uid', $nodeUid);
            $stmt->execute();
            $nodeRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt = $dbhTransact->prepare(
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
            return $nodeRow['parent_uid'];

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


