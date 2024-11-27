<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelItemInterface;

use Pes\Type\ContextData;

/**
 * Description of ViewModelAbstract
 *
 * @author pes2704
 */
class ViewModelItemAbstract extends ContextData implements ViewModelItemInterface {
    
    private $id;
    
    /**
     * {@inheritDoc}
     * 
     * @param type $id
     */
    public function setItemId($id) {
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
    public function getItemId() {
        return $this->id ?? null;
    }
}
