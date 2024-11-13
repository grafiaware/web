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
     * Metoda nastaví hodnotu identifikátoru entity, kterou viewModel čte z doménového modelu (z persistentních dat).
     * 
     * @param type $id
     */
    public function setIdentity($id);
    
    /**
     * Metoda nastaví hodnotu identifikátoru entity, kterou viewModel čte z doménového modelu (z persistentních dat).
     * 
     * @return bool
     */
    public function hasIdentity(): bool;
    
    /**
     * Metoda vrací hodnotu identifikátoru entity nastavenou metodou setId nebo null.
     */
    public function getIdentity();
}
