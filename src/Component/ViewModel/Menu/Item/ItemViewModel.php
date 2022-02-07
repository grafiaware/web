<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Menu\Item;

use Component\ViewModel\ViewModelAbstract;
use Red\Model\Entity\HierarchyAggregateInterface;

/**
 * Description of ItemViwModel
 *
 * @author pes2704
 */
class ItemViewModel extends ViewModelAbstract implements ItemViewModelInterface {

    /**
     * @var HierarchyAggregateInterface
     */
    private $hierarchyAggregate;

    private $realDepth;
    private $isOnPath;
    private $isLeaf;
    private $isPresented;
    private $pasteMode;
    private $isCutted;
    private $menuEditable;

    private $innerHtml;

    public function __construct(HierarchyAggregateInterface $hierarchaAggregate, $realDepth, $isOnPath, $isLeaf, $isPresented, $pasteMode, $isCutted, $menuEditable) {
        $this->hierarchyAggregate = $hierarchaAggregate;
        $this->realDepth = $realDepth;
        $this->isOnPath = $isOnPath;
        $this->isLeaf = $isLeaf;
        $this->isPresented = $isPresented;
        $this->pasteMode = $pasteMode;
        $this->isCutted = $isCutted;
        $this->menuEditable = $menuEditable;
        parent::__construct();
    }

    public function setInnerHtml($innerHtml): void {
        $this->innerHtml = $innerHtml;
    }

    /**
     *
     * @return HierarchyAggregateInterface
     */
    public function getHierarchyAggregate() {
        return $this->hierarchyAggregate;
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

    public function getInnerHtml() {
        return $this->innerHtml ?? '';
    }

    public function isMenuEditable() {
        return $this->menuEditable;
    }


}
