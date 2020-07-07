<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao\Hierarchy;

/**
 *
 * @author pes2704
 */
interface NodeEditDaoInterface {

#### pomocné čtecí metody ###################################################

    public function getNode($uid);

    public function getNodeByTitle($item);

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
     * Přesune podstrom (zadaný uzel a všechny jeho potomky) jako dítě cílového uzlu.
     *
     * @param type $sourceUid
     * @param type $targetUid
     * @throws Exception
     */
    public function moveSubTree($sourceUid, $targetUid);

    public function replaceNodeWithChild($nodeUid);

//    public function convertAdjacencyListToNestedSet();
}
