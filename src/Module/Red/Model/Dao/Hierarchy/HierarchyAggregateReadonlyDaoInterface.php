<?php

namespace Red\Model\Dao\Hierarchy;

use Model\Dao\DaoInterface;
use Model\Dao\DaoContextualInterface;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface HierarchyAggregateReadonlyDaoInterface extends DaoContextualInterface, DaoInterface {

    /**
     * JEN POMOCNÁ FUNKCE PRO LADĚNÍ
     * Vrací node podle hodnoty lang_code a title.
     * Hodnota title v řádku view nesmí být prázná, prázdnou nelze selectovat. Vybírá case insensitive. Pokud je více položek se stejným title, vrací jen první.
     *
     * @param string $langCode Hodnota existující v sloupci tabulky language.lang_code
     * @param string $title
     * @return array
     */
    public function getByTitleHelper(array $langCodeAndTitle): ?RowDataInterface ;

    /**
     * Full tree ve formě řazeného seznamu získaného traverzováním okolo stronu. V položkách seznamu vrací name, id, depth, breadcrumb.
     * Depth je hloubka položky ve stromu (kořen má hloubku 0), breadcrumb je řetězec "drobečkové navigace" - zřetězená
     *
     * @return array
     */
    public function getFullTree($langCode);

    /**
     * Subtree ve formě řazeného seznamu.
     * Vrací potomky rodičovského prvku. Pokud je zadána hloubka, vrací jen potomky do maximální hloubky jejich umístění v podstromu, jinak vrací celý podstrom.
     *
     * @param string $langCode
     * @param string $rootUid Uid kořenového prvku podstromu.
     * @param int $maxDepth
     * @return array
     */
    public function getSubTree($langCode, $rootUid, $maxDepth=NULL);

    /**
     * Vrací cestu od kořene stromu k zadanému prvku
     *
     * @param string $langCode
     * @param string $uid Uid zadaného prvku
     * @param bool $active
     * @param bool $actual
     * @return array
     */
    public function singlePath($langCode, $uid);

    /**
     * Vrací cestu od zadaného počátečného (kořenového) ke koncovému prvku cesty
     *
     * @param string $langCode
     * @param string $rootUid Uid počátečního prvku cesty
     * @param string $uid Uid koncového prvku cesty
     * @param bool $active
     * @param bool $actual
     * @return array
     */
    public function singleSubPath($langCode, $rootUid, $uid);

    /**
     * Vrací bezprostřední potomky
     *
     * @param string $langCode
     * @param string $uid
     * @param bool $active
     * @param type $actual
     * @return array
     */
    public function getImmediateSubNodes($langCode, $nodeUid);

    /**
     * Vrací rodiče (nikoli předky) prvku.
     *
     * @param type $langCode
     * @param type $uid
     */
    public function getParent($langCode, $uid): ?RowDataInterface ;

    /**
     * Vrací výběrový strom složený z uzlů na cestě od zadaného kořene k zadanému cílovému prvku a z sourozenců všech uzlů na cestě.
     *
     * Takový výběrový strom je vhodný pro zobrazení menu s rozbalenými (viditelnými) sourozenci každé položky na cestě k zobrazené (aktuálně vybrané) položce.
     *
     * @param type $langCode
     * @param type $rootUid
     * @param type $uid
     * @return type
     */
    public function getFullPathWithSiblings($langCode, $rootUid, $uid);

    /**
     * Vrací jednu položku. Položka obsahuje depth a breadcrumb.
     * Brad crumb je řetězec dvojic uid|title (uid a title jsou oddělené znakem |) oddělených navzájem znakem /
     *
     * @param string $langCode
     * @param string $uid
     * @return array
     */
    public function singleNodeBreadcrumb($langCode, $nodeUid);

    #################
    /**
     *
     * Find all leaf nodes
     *
     * @access public
     *
     * @return array
     *
     */
//    public function leafNodes();
}
