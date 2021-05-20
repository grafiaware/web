<?php

namespace Red\Model\Dao\Hierarchy;

//use Pes\Database\MyMiniHandler\HandlerInterface;
use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Statement\StatementInterface;

use Model\Context\ContextFactoryInterface;

/**
 * Podle tutoriálu na https://www.phpro.org/tutorials/Managing-Hierarchical-Data-with-PHP-and-MySQL.html - pozor jsou tam chyby
 * V tutoriálu jsou přepracované sql skripty, které zveřejnil http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/ - a od té doby je všichni kopíruji.
 *
 * Read hierarchy vybírá z "nested set", který vzinká z vícenásobného cross join tabulky nested_set a výsledek je inner join s menu_item.
 * Joiny nested setu trvají podle MySQL Workbench pro cca 500 položek (Grafia) méně než 1ms,
 * cross join s menu_item zvedne čas na 15-16ms a to bez ohledu na to jak složitá je podmínka ve "vnějším" selectu.
 *
 * Při výběru jen podle lang_code je výsledkem je "spojitý" strom - větve jsou celé, obsahují všechny hloubky.
 * Jde to, protože pro každou kombinaci uid a lang_code existuje položka.
 *
 * Pokud se vybírá i podle jiných sloupců - například podle menu_item.active nebo show_time,hide_time dojde k tomu,
 * že větve stromu nebudou celé - obashují např. uzel s hloubkou 1 a pak až uzel s hloukou 4.
 *
 * Tomu je třeba přizpůsobit renderování, například nerenderovat uzel, který má hloubky o více než 1 větší než aktuální hloubka.
 *
 * Druhá možnost je načítat po jednotlivých úrovních metodou getImmediateSubNodes(), tam mohou být podmínky na hodnoty sloupců a při rekurzi s opakovanám voláním getImmediateSubNodes()
 * není strom "děravý". Jenže metoda getImmediateSubNodes() trvá pro cca 500 položek (Grafia) cca 30-31 ms.
 *
 * V obou případech jsou např. publikované uzly, které mají nějakého nepublikovaného předka v menu nedostupné. Jsou jen v menu v "editačním" modu, kdy se zobrazijí i neaktivní a neaktuální uzly.
 */
class HierarchyAggregateReadonlyDao extends HierarchyAggregateEditDao implements HierarchyAggregateReadonlyDaoInterface {

    const UID_TITLE_SEPARATOR = '|';
    const BREADCRUMB_SEPARATOR = '/';

    private $itemTableName;

    /**
     *
     * @param HandlerInterface $handler
     * @param string $nestedSetTableName Jméno databázové tabulky menu nested set
     * @param string $itemTableName Jméno databázové tabulky menu item
     */
    public function __construct(HandlerInterface $handler, $nestedSetTableName, $itemTableName, ContextFactoryInterface $contextFactory=null) {
        parent::__construct($handler, $nestedSetTableName, $contextFactory);
        $this->itemTableName = $itemTableName;
    }

################
#
    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['active'] = "menu_item.active = 1";
                }
            }
        }
        return $contextConditions;
    }

    private function selected() {
        return "
	nested_set.uid, nested_set.depth, nested_set.left_node, nested_set.right_node, nested_set.parent_uid,
        menu_item.lang_code_fk, menu_item.uid_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.prettyuri, menu_item.active, menu_item.multipage
        ";
    }
#
#################

    /**
     * Vrací jeden node podle primárního klíče (kompozitní klíč lang_code_fk a uid_fk)
     * @param string $langCode Hodnota existující v sloupci tabulky language.lang_code
     * @param string $uid
     * @return array
     */
    public function get($langCode, $uid) {
        $sql =
            "SELECT "
            .$this->selected()
            ."FROM
                (SELECT
                    node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
                FROM
                    $this->nestedSetTableName AS node
                    CROSS JOIN
                    $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                WHERE
                    node.left_node BETWEEN parent.left_node AND parent.right_node
                GROUP BY node.uid
                ) AS nested_set
                    INNER JOIN
                $this->itemTableName AS menu_item ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code", "nested_set.uid = :uid"]));
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() == 1 ? $stmt->fetch(\PDO::FETCH_ASSOC) : NULL;
    }

    /**
     * JEN POMOCNÁ FUNKCE PRO LADĚNÍ
     * Vrací node podle title v tabulce $this->itemTableName - není nijak zaručena unikátnost title!
     * @param string $title
     * @return array
     */
    public function getByTitleHelper($langCode, $title) {
        $sql =
            "SELECT "
            .$this->selected()
            ."FROM
                (SELECT
                    node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
                FROM
                    $this->nestedSetTableName AS node
                    CROSS JOIN
                    $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                GROUP BY node.uid
                ) AS nested_set
                    INNER JOIN
                $this->itemTableName AS menu_item ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code", "menu_item.title = :title"]))
                ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() >= 1 ? $stmt->fetch(\PDO::FETCH_ASSOC) : NULL;
    }

    /**
     * Full tree ve formě řazeného seznamu získaného traverzováním okolo stronu. V položkách seznamu vrací name, id, depth, breadcrumb.
     * Depth je hloubka položky ve stromu (kořen má hloubku 0), breadcrumb je řetězec "drobečkové navigace" - zřetězená
     *
     * @return type
     */
    public function getFullTree($langCode) {
//        $stmt = $this->dbHandler->prepare(
//            " SELECT node.title, node.uid, (COUNT(parent.uid ) - 1) AS depth, GROUP_CONCAT(DISTINCT parent.title  ORDER BY parent.uid ASC SEPARATOR ' / ') AS breadcrumb
//            FROM $this->nestedSetTableName AS node CROSS JOIN $this->nestedSetTableName AS parent
//            WHERE node.left_node BETWEEN parent.left_node AND parent.right_node AND node.lang_code = :lang_code
//            GROUP BY node.uid
//            ORDER BY node.left_node");

        $sql =
            "SELECT "
            .$this->selected()
            ."FROM
                (SELECT
                    node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
                FROM
                    $this->nestedSetTableName AS node
                    CROSS JOIN
                    $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                GROUP BY node.uid) AS nested_set
                    INNER JOIN
                $this->itemTableName ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code"]))
            ." ORDER BY nested_set.left_node"
                ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

    /**
     * Subtree ve formě řazeného seznamu.
     *
     * @param string $langCode
     * @param string $rootUid Uid kořenového prvku podstromu.
     * @param int $maxDepth
     * @return array
     */
    public function getSubTree($langCode, $rootUid, $maxDepth=NULL){
        $sql =
             "SELECT "
            .$this->selected()
            ."FROM
                (SELECT
                    node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
                FROM
                    $this->nestedSetTableName AS node
                    CROSS JOIN
                    $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                    CROSS JOIN
                    $this->nestedSetTableName AS sub_parent ON node.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
                WHERE sub_parent.uid = :uid
                GROUP BY node.uid "
                .(isset($maxDepth) ? "HAVING depth BETWEEN 1 AND :maxdepth" : "")
                .") AS nested_set
                    INNER JOIN
                $this->itemTableName ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code"]))
            ." ORDER BY nested_set.left_node"
                ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':uid', $rootUid, \PDO::PARAM_STR);
        if(isset($maxDepth)) {
            $stmt->bindParam(':maxdepth', $maxDepth, \PDO::PARAM_INT);
        }
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

    /**
     * Vrací cestu od kořene stromu k zadanému prvku
     *
     * @param string $langCode
     * @param string $uid Uid zadaného prvku
     * @return array
     */
    public function singlePath($langCode, $uid){
        $sql =
             "SELECT "
            .$this->selected()
            ."FROM
                (SELECT
                    parent.uid, (COUNT(grand_parent.uid) - 1) AS depth, parent.left_node, parent.right_node, parent.parent_uid
                FROM
                    $this->nestedSetTableName AS node
                    CROSS JOIN
                    $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                    CROSS JOIN
                    $this->nestedSetTableName AS grand_parent ON parent.left_node BETWEEN grand_parent.left_node AND grand_parent.right_node
                WHERE node.uid = :uid
                GROUP BY parent.uid) AS nested_set
                    INNER JOIN
                $this->itemTableName ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code"]))
                ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

    /**
     * Vrací cestu od zadaného počátečného (kořenového) ke koncovému prvku cesty
     *
     * @param string $langCode
     * @param string $rootUid Uid počátečního prvku cesty
     * @param string $uid Uid koncového prvku cesty
     * @return array
     */
    public function singleSubPath($langCode, $rootUid, $uid){
        $sql =
             "SELECT "
            .$this->selected()
            ."FROM
                (SELECT
                    parent.uid, (COUNT(grand_parent.uid) - 1) AS depth, parent.left_node, parent.right_node, parent.parent_uid
                    ,
                    (SELECT COUNT(*)
                       FROM $this->nestedSetTableName AS middle_parent
                       WHERE middle_parent.left_node BETWEEN grand_parent.left_node AND grand_parent.right_node
                         AND parent.left_node BETWEEN middle_parent.left_node AND middle_parent.right_node
                       ) AS depth
                FROM
                    $this->nestedSetTableName AS grand_parent
                    INNER JOIN
                    $this->nestedSetTableName AS parent ON parent.left_node BETWEEN grand_parent.left_node AND grand_parent.right_node
                    INNER JOIN
                    $this->nestedSetTableName AS node ON node.left_node BETWEEN parent.left_node AND parent.right_node
                WHERE grand_parent.uid=:rootuid
                      AND node.uid=:uid
                  )  AS nested_set
                INNER JOIN
                $this->itemTableName ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code"]))
            ." ORDER BY nested_set.left_node"
                ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':rootuid', $rootUid, \PDO::PARAM_STR);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }
    /**
     * Vrací bezprostřední potomky
     *
     * @param string $langCode
     * @param string $uid
     * @return array
     */
    public function getImmediateSubNodes($langCode, $uid){
        return $this->getSubNodes($langCode, $uid, 1);
    }

    /**
     * Vrací potomky rodičovského prvku. Pokud je zadán, vrací jen potomky do maximální hloubky jejich umístění v celém stromu, jinak vrací celý postrom.
     *
     * @param string $langCode
     * @param string $parentUid uid rodičovského porvku
     * @param type $maxDepth
     * @return array
     */
    public function getSubNodes($langCode, $parentUid, $maxDepth=NULL){
        $sql =
            "SELECT "
            .$this->selected()
            ."FROM
                (
                SELECT
                    node.uid, (COUNT(node.uid)-1) AS depth, node.left_node, node.right_node, node.parent_uid
                FROM
                    $this->nestedSetTableName AS node
                        CROSS JOIN
                    $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                        CROSS JOIN
                    $this->nestedSetTableName AS sub_parent ON parent.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
                WHERE sub_parent.uid = :uid

                GROUP BY node.uid"
//            ."FROM
//                (SELECT
//                    node.uid, (COUNT(parent.uid) -1) AS depth, (COUNT(parent.uid) - sub_tree.depth -1) AS nsdepth, node.left_node, node.right_node, node.parent_uid
//                FROM
//                    $this->nestedSetTableName AS node
//                    CROSS JOIN
//                    $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
//                    CROSS JOIN
//                    $this->nestedSetTableName AS sub_parent ON node.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
//                    CROSS JOIN
//                    (SELECT
//                        node.uid, (COUNT(parent.uid) - 1) AS depth
//                        FROM
//                            $this->nestedSetTableName AS node
//                            CROSS JOIN
//                            $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
//                        WHERE node.uid = :uid
//                        GROUP BY node.uid
//                        ORDER BY node.left_node) AS sub_tree
//                WHERE sub_parent.uid = sub_tree.uid
//                GROUP BY node.uid "
                .(isset($maxDepth) ? " HAVING depth BETWEEN 1 AND :maxdepth " : "")
                .") AS nested_set
                    INNER JOIN
                $this->itemTableName ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code"]))
                ." ORDER BY nested_set.left_node"
            ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':uid', $parentUid, \PDO::PARAM_STR);
        if(isset($maxDepth)) {
            $stmt->bindParam(':maxdepth', $maxDepth, \PDO::PARAM_INT);
        }
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

    public function getFullPathWithSiblings($langCode, $rootUid, $uid) {
        $sql =
            "SELECT "
            .$this->selected()
            ."FROM
                (SELECT
                    sub.uid, (COUNT(sub.uid) ) AS depth, sub.left_node, sub.right_node, sub.parent_uid
                FROM
                    $this->nestedSetTableName AS node
                        CROSS JOIN
                    $this->nestedSetTableName AS parent
                        CROSS JOIN
                    $this->nestedSetTableName AS sub
                        CROSS JOIN
                    $this->nestedSetTableName AS sub_parent
                WHERE
                    node.left_node BETWEEN parent.left_node AND parent.right_node
                        AND node.uid = :uid
                        AND sub.left_node BETWEEN parent.left_node AND parent.right_node
                        AND sub_parent.uid = parent.uid
                        AND sub.left_node BETWEEN sub_parent.left_node AND sub_parent.right_node
                GROUP BY sub.uid
                ORDER BY sub.left_node
                ) AS nested_set
                    INNER JOIN
                $this->itemTableName ON (nested_set.uid = menu_item.uid_fk)"
                .$this->where($this->and($this->getContextConditions(), ["menu_item.lang_code_fk = :lang_code"]))
            ." ORDER BY nested_set.left_node"
            ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
//                                AND sub_parent.uid = :rootuid

//        $stmt->bindParam(':rootuid', $rootUid, \PDO::PARAM_STR);
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

    /**
     * Vrací jednu položku. Položka obsahuje depth a breadcrumb.
     * Brad crumb je řetězec dvojic uid|title (uid a title jsou oddělené znakem |) oddělených navzájem znakem /
     *
     * @param string $langCode
     * @param string $uid
     * @return array
     */
    public function singleNodeBreadcrumb($langCode, $uid){
        $sql =
            "SELECT
                (COUNT(parent.uid)) AS depth,
                GROUP_CONCAT(DISTINCT menu_item_for_breadcrumb.uid_fk,
                    CONCAT(menu_item_for_breadcrumb.uid_fk,
                            ' | ',
                            menu_item_for_breadcrumb.title)
                    ORDER BY menu_item_for_breadcrumb.uid_fk ASC
                    SEPARATOR ' / ') AS breadcrumb
            FROM
                $this->nestedSetTableName AS node
                    CROSS JOIN
                $this->nestedSetTableName AS parent ON node.left_node BETWEEN parent.left_node AND parent.right_node
                    INNER JOIN
                $this->itemTableName AS menu_item_for_breadcrumb ON (parent.uid = menu_item_for_breadcrumb.uid_fk)
                    INNER JOIN
                $this->itemTableName AS menu_item_for_title ON (node.uid = menu_item_for_title.uid_fk)
            WHERE node.uid = :uid
                    AND menu_item_for_breadcrumb.lang_code_fk = :lang_code
                    AND menu_item_for_title.lang_code_fk = :lang_code"
               ;
        $stmt = $this->dbHandler->prepare($sql);
        $stmt->bindParam(':uid', $uid, \PDO::PARAM_STR);
        $stmt->bindParam(':lang_code', $langCode, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
    }

#################
    /**
     * METODA NENÍ POUŽITA - VYMYKÁ SE, NEVRACÍ DEPTH A VYŽADOVALA BY TAK POUŽITÍ JINÉ ENTITY.
     * Vrací všechny listy
     *
     * @return array
     */
//    public function leafNodes(){
//        $stmt = $this->dbHandler->prepare(
//                "SELECT menu_item.title, nested_set.uid
//                FROM $this->nestedSetTableName AS nested_set
//                    INNER JOIN
//                    $this->itemTableName AS menu_item
//                WHERE nested_set.right_node = nested_set.left_node + 1");
//        $stmt->execute();
//        return $stmt->fetchALL(\PDO::FETCH_ASSOC);
//    }



}
