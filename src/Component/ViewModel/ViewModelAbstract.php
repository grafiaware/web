<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

use Pes\Type\ContextData;

/**
 * Description of ViewModelAbstract
 *
 * @author pes2704
 */
class ViewModelAbstract extends ContextData implements ViewModelInterface {
    
    private $id;
    
    /**
     * {@inheritDoc}
     * 
     * @param type $id
     */
    public function setRequestedId($id) {
        $this->id = $id;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return bool
     */
    public function hasRequestedId(): bool {
        return isset($this->id);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return mixed
     */
    public function getRequestedId() {
        return $this->id ?? null;
    }
}
