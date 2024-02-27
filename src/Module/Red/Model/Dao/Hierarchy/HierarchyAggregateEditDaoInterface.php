<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao\Hierarchy;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;

/**
 *
 * @author pes2704
 */
interface HierarchyAggregateEditDaoInterface extends HierarchyAggregateReadonlyDaoInterface {

#### helper #################################################
    /**
     * Pomocná metoda - čte data jen z db tabulky pro nested set (hierarchy), nikoli agregátní
     *
     * @param string $uid
     */
    public function getParentNodeHelper($uid);

#### editační metody ###################################################;#####
    /**
     * Vloží nový kořenový uzel nested setu. Uzel má parametry left node = 1, right node = 2 a uid rodiče = NULL. Další uzly nested setu pak je vhodné přidávat
     * metodami addChildNode() a addChildNodeAsLast().
     *
     * @return integer Automaticky generované uid vloženého uzlu
     * @throws LogicException Pokud tabulka pro ukládání nested setu není prázdná.
     */
    public function newNestedSet();

    /**
     * Vloží nový uzel. Neprovádí žádné kontroly. Vrací nově vygenerovaný uid.
     *
     * @param integer $leftNode
     * @param integer $rightNode
     * @param type $parentNodeUid Je možnézadat uid rodiče. To se nepoužívá pro funkce nested set (hierarchy), ale může být použito pro rekonstrukci hierarchie v případě havárie (rozpadu) struktury nested setu.
     * @return integer Automaticky generované uid vloženého uzlu
     * @throws Exception
     */
    public function insertNode($leftNode, $rightNode, $parentNodeUid=NULL);


    /**
     * Přidá uzel jako potomka zadaného uzlu. Vrací automaticky generované uid vloženého uzlu.
     * @param int $parentNodeUid
     * @return int Automaticky generované uid vloženého uzlu
     * @throws Exception
     */
    public function addChildNode($parentNodeUid);

    /**
     * Přidá uzel jako potomka zadaného uzlu. Vrací automaticky generované uid vloženého uzlu.
     * @param int $parentNodeUid
     * @return int Automaticky generované uid vloženého uzlu
     * @throws Exception
     */
    public function addChildNodeAsLast($parentNodeUid);


    /**
     * Přidá uzel jako sourozence zadaného uzlu, t.j. na stejnou úroveň. Vrací automaticky generované uid vloženého uzlu.
     *
     * @param int $leftNodeUid
     * @return int Automaticky generované uid vloženého uzlu
     * @throws Exception
     */
    public function addNode($leftNodeUid);


    /**
     * @Delete a leaf node
     *
     * @param string $nodeUid
     */
    public function deleteLeafNode($nodeUid);

    /**
     * @Delete a top node and all its children
     *
     * @param string $nodeUid
     */
    public function deleteSubTree($nodeUid);

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
    public function moveSubTreeAsChild($sourceUid, $targetUid, $deactivate=true): void;

    /**
     * Přesune podstrom (zdrojový uzel a všechny jeho potomky) jako sourozence cílového uzlu. Vloží podstrom vpravo od cílového uzlu.
     * Defaultně deaktivuje všechny položky menu příslušné k přesunutým uzlům.
     *
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu.
     *
     * @param string $sourceUid uid zdrojového uzlu
     * @param string $targetUid uid cílového uzlu
     * @param bool $deactivate
     * @return void
     * @throws Exception
     */
    public function moveSubTreeAsSiebling($sourceUid, $targetUid, $deactivate=true): void;

    /**
     * Zkopíruje podstrom (zdrojový uzel a všechny jeho potomky) jako dítě cílového uzlu. Zkopíroje také položky menu (menu item).
     * Defaultně zkopírované položky nastaví jako neaktivní (nepublikované).
     *
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu.
     *
     * @param type $deactivate
     * @return array
     * @throws Exception
     */
    public function copySubTreeAsChild($sourceUid, $targetUid, $deactivate=true): array;

    /**
     * Zkopíruje podstrom (zdrojový uzel a všechny jeho potomky) jako sourozence cílového uzlu. Vloží podstrom vpravo od cílového uzlu. Zkopíroje také položky menu (menu item).
     * Defaultně zkopírované položky nastaví jako neaktivní (nepublikované).
     *
     * Výskyt aktivní položky mezi potomky neaktivní položky způsobí chyby při renderování stromu menu v needitačním režimu.
     *
     * @param type $sourceUid
     * @param type $targetUid
     * @param type $deactivate
     * @return array
     * @throws Exception
     */
    public function copySubTreeAsSiebling($sourceUid, $targetUid, $deactivate=true): array;

    /**
     * Smaže uzel a jeho potomky posune na jeho místo.
     *
     * @param type $nodeUid
     * @return void
     * @throws Exception
     */
    public function replaceNodeWithChild($nodeUid): void;

//    public function convertAdjacencyListToNestedSet();
}
