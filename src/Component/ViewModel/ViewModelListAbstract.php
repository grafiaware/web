<?php
namespace Component\ViewModel;
use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelListInterface;
use Component\ViewModel\FamilyInterface;
use Component\ViewModel\ViewModelLimitedListInterface;

/**
 * Description of ViewModelListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelListAbstract extends ViewModelAbstract implements ViewModelListInterface {
    
    protected $listRepo;
    
    protected $listEntities = [];    
        
    /**
     * Metoda musí načíst z databáze všchny entity pro list a vložit je do pole $this->listEntities
     */
    abstract protected function loadListEntities();

    
}
