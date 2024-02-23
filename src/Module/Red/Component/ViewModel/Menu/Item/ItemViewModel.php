<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Menu\Item;

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

    private $pageHref;
    private $redApiUri;
    private $title;
    private $active;
    private $realDepth;
    private $isOnPath;
    private $isLeaf;
    private $isPresented;
    private $isRoot;
    private $pasteMode;
    private $isCutted;
    private $menuEditable;

    private $child;

    public function __construct(HierarchyAggregateInterface $hierarchaAggregate, 
            $pageHref, $redApiUri, $title, $active, $realDepth, $isOnPath, $isLeaf, $isPresented, $isRoot, $isCutted, $pasteMode, $menuEditable) {

        $this->hierarchyAggregate = $hierarchaAggregate;
        
        $this->pageHref = $pageHref;
        $this->redApiUri = $redApiUri;
        $this->title = $title;
        $this->active = $active;
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

    public function getChild(): ?ComponentInterface {
        return $this->child;
    }
    
    #############
    
    /**
     *
     * @return HierarchyAggregateInterface
     */
    public function getHierarchyAggregate() {
        return $this->hierarchyAggregate;
    }
    
    #############
    
    public function getPageHref() {
        return $this->pageHref;
    }
    
    public function getRedApiUri() {
        return $this->redApiUri;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getActive() {
        return $this->active;
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

    public function isMenuEditable() {
        return $this->menuEditable;
    }
}
