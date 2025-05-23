<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelItemInterface;

/**
 * Description of ViewModelAbstract
 *
 * @author pes2704
 */
abstract class ViewModelItemAbstract extends ViewModelAbstract implements ViewModelItemInterface {
    
    private $id;
    
    /**
     * {@inheritDoc}
     * 
     * @param type $id
     */
    public function setItemId(string $id) {
        $this->id = $id;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return bool
     */
    public function hasItemId(): bool {
        return isset($this->id);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return mixed
     */
    public function getItemId(): string {
        return $this->id ?? null;
    }
}
