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
use Component\ViewModel\ViewModelLimitedListInterface;

/**
 * Description of ViewModelAbstract
 *
 * @author pes2704
 */
abstract class ViewModelSingleListAbstract extends ViewModelListAbstract implements SingleInterface {
    
    use SingleTrait;
    
    public function provideItemEntityCollection(): iterable {
        $this->loadListEntities();
        if ($this->isListEditable()) { 
            if ($this instanceof ViewModelLimitedListInterface) {
                if ($this->isItemCountUnderLimit()) {
                    $this->listEntities[] = $this->newListEntity();
                }
            } else {
                $this->listEntities[] = $this->newListEntity();
            }
        }
        return $this->listEntities;        
    }
}
