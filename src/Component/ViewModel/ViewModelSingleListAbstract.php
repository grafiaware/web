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
    
    /**
     * Volá metodu loadListEntities() konkrétního single list view modelu (XXXSingleListViewModel) pro načtení všech entit pro list z databáze.
     * Pokud je editovatelný mód (list je editovatelný) a není překročen maximální počet entit (pro typ ViewModelLimitedListInterface) 
     * přidá jednu novou entitu (pro přidání položky do seznamu ve formuláři).
     * 
     * @return iterable
     */
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
