<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Pes\Type\ContextDataInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelInterface extends ContextDataInterface {
    
    /**
     * Metoda nastaví hodnotu identifikátoru z requestu.
     * 
     * @param type $id
     */
    public function setRequestedId($id);
    
    /**
     * Informuje, že ViewModel má nastavenou hodnotu identifikátoru z requestu..
     * 
     * @return bool
     */
    public function hasRequestedId(): bool;
    
    /**
     * Metoda vrací hodnotu identifikátoru z requestu nastavenou metodou setIdentity() nebo null.
     */
    public function getRequestedId();
}
