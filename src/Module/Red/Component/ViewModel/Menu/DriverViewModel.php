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

    private $uid;
    
    private $pageHref;
    private $redApiUri;
    private $title;
    private $active;
    private $realDepth;
    private $isPresented;
    private $pasteMode;
    private $isCutted;
    private $menuEditable;

    private $child;

    public function __construct($uid, $pageHref, $redApiUri, $title, $active, $isPresented, $isCutted, $pasteMode, $menuEditable) {
        
        $this->uid = $uid;
        
        $this->pageHref = $pageHref;
        $this->redApiUri = $redApiUri;
        $this->title = $title;
        $this->active = $active;
        $this->isPresented = $isPresented;
        $this->isCutted = $isCutted;
        $this->pasteMode = $pasteMode;
        $this->menuEditable = $menuEditable;
        parent::__construct();
    }
    
    public function getUid() {
        return $this->uid;
    }
    
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

    public function isPresented() {
        return $this->isPresented;
    }
    
    public function isMenuEditable() {
        return $this->menuEditable;
    }
}
