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
class ItemViewModel extends ViewModelAbstract implements ItemViewModelInterface {

    private $uid;
    
    private $pageHref;
    private $redApiUri;
    private $title;
    private $active;
    private $isPresented;

    private $child;

    public function __construct($uid, $pageHref, $redApiUri, $title, $active, $isPresented) {
        
        $this->uid = $uid;
        
        $this->pageHref = $pageHref;
        $this->redApiUri = $redApiUri;
        $this->title = $title;
        $this->active = $active;
        $this->isPresented = $isPresented;
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
    
    public function isActive() {
        return $this->active;
    }

    public function isPresented() {
        return $this->isPresented;
    }
}
