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
   
    abstract protected function loadListEntities();
    
    abstract protected function newListEntity();
    
    abstract protected function cardinality();
    
    public function provideItemEntityCollection(): iterable {
        $this->loadListEntities();
        if ($this->isListEditable()) { 
            if ($this instanceof ViewModelLimitedListInterface) {
                if ($this->isItemCountUnderLimit()) {
                    $this->addNewEntity();                    
                }
            } else {
                $this->addNewEntity();
            }
        }
        return $this->listEntities;        
    }
    
    private function addNewEntity() {
        $cadinality = $this->cardinality();
        switch ($cadinality) {
            case FamilyInterface::CARDINALITY_0_1:
                // exc
            case FamilyInterface::CARDINALITY_1_1:
                if ($this->isListEditable() && count($this->listEntities)<1) {   // přidat jen pokud entita není
                    $this->listEntities[] = $this->newListEntity();
                }
                break;
            case FamilyInterface::CARDINALITY_0_N:
                // exc
            case FamilyInterface::CARDINALITY_1_N:
                    $this->listEntities[] = $this->newListEntity();                
                break;            
        }
    }
    
}
