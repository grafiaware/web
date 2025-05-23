<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Model\Entity\EntityInterface;
use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelItemInterface extends ViewModelInterface {
    
    public function receiveEntity(EntityInterface $entity);
        
    public function isItemEditable(): bool;
    
    /**
     * Metoda nastaví hodnotu identifikátoru z requestu.
     * 
     * @param type $id
     */
    public function setItemId(string $id);
    
    /**
     * Informuje, že ViewModel má nastavenou hodnotu identifikátoru z requestu..
     * 
     * @return bool
     */
    public function hasItemId(): bool;
    
    /**
     * Metoda vrací hodnotu identifikátoru z requestu nastavenou metodou setIdentity() nebo null.
     */
    public function getItemId(): string;
//    
}
