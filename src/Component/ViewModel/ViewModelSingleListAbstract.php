<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelListAbstract;
use Component\ViewModel\SingleInterface;
use Component\ViewModel\SingleTrait;

/**
 * Description of ViewModelAbstract
 *
 * @author pes2704
 */
abstract class ViewModelSingleListAbstract extends ViewModelListAbstract implements SingleInterface {
    
    use SingleTrait;
}
