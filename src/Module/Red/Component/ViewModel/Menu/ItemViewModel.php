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
 * Description of ItemViwModel
 *
 * @author pes2704
 */
class ItemViewModel extends ViewModelAbstract implements ItemViewModelInterface {

    /**
     * @var HierarchyAggregateInterface
     */
//    private $hierarchyAggregate;
    
    private $realDepth;
    private $isOnPath;
    private $isLeaf;
    private $isPresented;
    private $isRoot;

    private $child;

    public function __construct(
//            HierarchyAggregateInterface $hierarchaAggregate, 
              $realDepth, $isOnPath, $isLeaf, $isPresented, $isRoot) {

//        $this->hierarchyAggregate = $hierarchaAggregate;
        
        $this->realDepth = $realDepth;
        $this->isOnPath = $isOnPath;
        $this->isLeaf = $isLeaf;
        $this->isPresented = $isPresented;
        $this->isRoot = $isRoot;
        parent::__construct();
    }
    public function appendDriver(ComponentInterface $driverComponent): void {
        ;
    }
    
    public function getDriver(): ?ComponentInterface {
        ;
    }
    
    public function appendLevel(ComponentInterface $child): void {
        $this->child = $child;
    }

    public function getLevel(): ?ComponentInterface {
        return $this->child;
    }
    
    #############
    
    /**
     *
     * @return HierarchyAggregateInterface
     */
//    public function getHierarchyAggregate() {
//        return $this->hierarchyAggregate;
//    }
    
    #############

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
}
