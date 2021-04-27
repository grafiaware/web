<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Menu\Item;

use Model\Entity\HierarchyAggregateInterface;

/**
 * Description of ItemViwModel
 *
 * @author pes2704
 */
class ItemViewModel implements ItemViewModelInterface {

    /**
     * @var HierarchyAggregateInterface
     */
    private $menuNode;

    private $realDepth;
    private $isOnPath;
    private $isLeaf;
    private $isPresented;
    private $isEditableItem;
    private $pasteMode;
    private $isCutted;

    private $innerHtml;
    private $pasteUid;

    public function __construct(HierarchyAggregateInterface $menuNode, $realDepth, $isOnPath, $isLeaf, $isPresented, $editableItem, $pasteMode, $isCutted, $pasteUid=null) {
        $this->menuNode = $menuNode;
        $this->realDepth = $realDepth;
        $this->isOnPath = $isOnPath;
        $this->isLeaf = $isLeaf;
        $this->isPresented = $isPresented;
        $this->isEditableItem = $editableItem;
        $this->pasteMode = $pasteMode;
        $this->isCutted = $isCutted;
        $this->pasteUid = $pasteUid;
    }

    public function setInnerHtml($innerHtml): void {
        $this->innerHtml = $innerHtml;
    }

    /**
     *
     * @return HierarchyAggregateInterface
     */
    public function getMenuNode() {
        return $this->menuNode;
    }
    public function getRealDepth() {
        return $this->realDepth;
    }

    public function isOnPath() {
        return $this->isOnPath;
    }

    public function isLeaf() {
        return $this->isLeaf;
    }

    public function isPresented() {
        return $this->isPresented;
    }
    public function isPasteMode() {
        return $this->pasteMode;
    }
    public function isCutted() {
        return $this->isCutted;
    }

    public function isEditableItem() {
        return $this->isEditableItem;
    }

    public function getInnerHtml() {
        return $this->innerHtml ?? '';
    }

    public function getPasteUid() {
        return $this->pasteUid;
    }
}
