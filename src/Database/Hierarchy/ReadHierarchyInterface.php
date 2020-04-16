<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Database\Hierarchy;

/**
 *
 * @author pes2704
 */
interface ReadHierarchyInterface {

    /**
     *
     * @param string $langCode Hodnota existující v sloupci tabulky language.lang_code
     * @param string $uid
     * @return array
     */
    public function getNode($langCode, $uid, $active, $actual);

    /**
     * JEN POMOCNÁ FUNKCE PRO LADĚNÍ
     * Vrací node podle hodnoty lang_code a title.
     * Hodnota title v řádku view nesmí být prázná, prázdnou nelze selectovat. Vybírá case insensitive. Pokud je více položek se stejným title, vrací jen první.
     *
     * @param string $langCode Hodnota existující v sloupci tabulky language.lang_code
     * @param string $title
     * @return array
     */
    public function getNodeByTitle($langCode, $title, $active, $actual);

    /**
     * Full tree ve formě řazeného seznamu získaného traverzováním okolo stronu. V položkách seznamu vrací name, id, depth, breadcrumb.
     * Depth je hloubka položky ve stromu (kořen má hloubku 0), breadcrumb je řetězec "drobečkové navigace" - zřetězená
     *
     * @return array
     */
    public function getFullTree($langCode, $active, $actual);

    /**
     * Subtree ve formě řazeného seznamu.
     *
     * @param string $langCode
     * @param string $rootUid Uid kořenového prvku podstromu.
     * @param bool $active
     * @param bool $actual
     * @param int $maxDepth
     * @return array
     */
    public function getSubTree($langCode, $rootUid, $active, $actual, $maxDepth=NULL);

    /**
     * Vrací cestu od kořene stromu k zadanému prvku
     *
     * @param string $langCode
     * @param string $uid Uid zadaného prvku
     * @param bool $active
     * @param bool $actual
     * @return array
     */
    public function singlePath($langCode, $uid, $active, $actual);

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
    public function singleSubPath($langCode, $rootUid, $uid, $active, $actual);

    /**
     * Vrací bezprostřední potomky
     *
     * @param string $langCode
     * @param string $uid
     * @param bool $active
     * @param type $actual
     * @return array
     */
    public function getImmediateSubNodes($langCode, $nodeUid, $active, $actual);

    /**
     * Vrací potomky rodičovského prvku. Pokud je zadán, vrací jen potomky do maximální hloubky jejich umístění v celém stromu, jinak vrací celý postrom.
     *
     * @param string $langCode
     * @param string $parentUid uid rodičovského porvku
     * @param type $active
     * @param type $actual
     * @param type $maxDepth
     * @return array
     */
    public function getSubNodes($langCode, $parentUid, $active, $actual, $maxDepth=NULL);

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
