<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Component\View;
use Component\ViewModel\ViewModelCollectionInterface;
/**
 *
 * @author pes2704
 */
interface ComponentListInterface {
    public function setCollectionViewModel(ViewModelCollectionInterface $collectionViewModel);
}
