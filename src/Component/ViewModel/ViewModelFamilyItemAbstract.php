<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\FamilyInterface;
use Component\ViewModel\ViewModelItemAbstract;
use Component\ViewModel\FamilyTrait;
/**
 * 
 * Description of ViewModelAbstract
 *
 * @author pes2704
 */
abstract class ViewModelFamilyItemAbstract extends ViewModelItemAbstract implements FamilyInterface {
    
    use FamilyTrait;

}
