<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Menu\Item;

use Component\ViewModel\ViewModelAbstract;
use Red\Model\Entity\HierarchyAggregateInterface;
use Component\View\ComponentInterface;

/**
 * Description of ItemViwModel
 *
 * @author pes2704
 */
class ItemViewModel extends ViewModelAbstract implements ItemViewModelInterface {

    private $uniqid;


    /**
     * @var HierarchyAggregateInterface
     */
    private $hierarchyAggregate;

    private $realDepth;
    private $isOnPath;
    private $isLeaf;
    private $isPresented;
    private $isRoot;
    private $pasteMode;
    private $isCutted;
    private $menuEditable;

    private $child;

    public function __construct(HierarchyAggregateInterface $hierarchaAggregate, $realDepth, $isOnPath, $isLeaf, $isPresented, $isRoot, $isCutted, $pasteMode, $menuEditable) {

        $this->uniqid = uniqid();

        $this->hierarchyAggregate = $hierarchaAggregate;
        $this->realDepth = $realDepth;
        $this->isOnPath = $isOnPath;
        $this->isLeaf = $isLeaf;
        $this->isPresented = $isPresented;
        $this->isRoot = $isRoot;
        $this->isCutted = $isCutted;
        $this->pasteMode = $pasteMode;
        $this->menuEditable = $menuEditable;
        parent::__construct();
    }

    public function hydrateChild(ComponentInterface $child): void {
        $this->child = $child;
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

    public function isRoot() {
        return $this->isRoot;
    }

    public function isPasteMode() {
        return $this->pasteMode;
    }
    public function isCutted() {
        return $this->isCutted;
    }

    public function getChild(): ?ComponentInterface {
        return $this->child;
    }

    public function isMenuEditable() {
        return $this->menuEditable;
    }


}
