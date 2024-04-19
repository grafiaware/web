<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Menu;

use Component\ViewModel\ViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface DriverViewModelInterface extends ViewModelInterface {
    public function withMenuItem(MenuItemInterface $menuItem): DriverViewModelInterface;
    public function setEditable(bool $editable);
    public function setItemType($itemType);
    public function getItemType();
    public function isActive();
    public function isPresented();
    public function presentEditableMenu(): bool;    
    public function isPasteMode(): bool;    
    
    public function getUid();
    public function getId();
    public function getPageApi();
    public function getRedContentApi();
    public function getRedDriverApi();
    public function getTitle();    
}
