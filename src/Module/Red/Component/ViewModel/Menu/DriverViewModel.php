<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Menu;

use Component\ViewModel\ViewModelAbstract;
use Red\Model\Entity\HierarchyAggregateInterface;
use Component\View\ComponentInterface;

/**
 * Description of DriverViewModel
 *
 * @author pes2704
 */
class DriverViewModel extends ViewModelAbstract implements DriverViewModelInterface {

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
    
    public function getPageHref() {
        return $this->pageHref;
    }
    
    public function getRedApiUri() {
        return $this->redApiUri;
    }
    
    public function getTitle() {
        return $this->title;
    }

    public function isPasteMode() {
        return $this->pasteMode;
    }
    public function isCutted() {
        return $this->isCutted;
    }
    
    public function isActive() {
        return $this->active;
    }

    public function isMenuEditable() {
        return $this->menuEditable;
    }
}
