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

    private $isOnPath;
    private $isLeaf;
    private $isPresented;
    private $isCutted;
    private $readonly;
    private $innerHtml;
    private $modeCommand;

    public function __construct(HierarchyAggregateInterface $menuNode, $isOnPath, $isPresented, $isCutted, $readonly) {
        $this->menuNode = $menuNode;
        $this->isOnPath = $isOnPath;
        $this->isPresented = $isPresented;
        $this->isCutted = $isCutted;
        $this->readonly = $readonly;

        $this->isLeaf = ($this->menuNode->getRightNode() - $this->menuNode->getLeftNode()) == 1;
    }

    public function setInnerHtml($innerHtml): void {
        $this->innerHtml = $innerHtml;
    }

    public function setModeCommand($mode) {
        $this->modeCommand = $mode;
    }

    /**
     *
     * @return HierarchyAggregateInterface
     */
    public function getMenuNode() {
        return $this->menuNode;
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

    public function isCutted() {
        return $this->isCutted;
    }

    public function isReadonly() {
        return $this->readonly;
    }

    public function getInnerHtml() {
        return $this->innerHtml ?? '';
    }

    public function getModeCommand() {
        return $this->modeCommand;
    }
}
