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

    private $pageHref;
    private $redApiUri;
    private $title;
    private $active;
    private $realDepth;
    private $pasteMode;
    private $isCutted;
    private $menuEditable;

    private $child;

    public function __construct($pageHref, $redApiUri, $title, $active, $realDepth, $isCutted, $pasteMode, $menuEditable) {
        
        $this->pageHref = $pageHref;
        $this->redApiUri = $redApiUri;
        $this->title = $title;
        $this->active = $active;
        $this->realDepth = $realDepth;
        $this->isCutted = $isCutted;
        $this->pasteMode = $pasteMode;
        $this->menuEditable = $menuEditable;
        parent::__construct();
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

    public function isMenuEditable() {
        return $this->menuEditable;
    }
}
