<?php
namespace Component\ViewModel;
use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelMultiInterface;

/**
 * Description of ViewModelListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelMultiAbstract extends ViewModelAbstract implements ViewModelMultiInterface {
    
    protected $listRepo;
    
    protected $multiEntitiesMap = [];
    protected $selectedEntities = [];
    
    abstract protected function loadMultiEntitiesMap();
    
    abstract protected function newMultiEntity();
    
    abstract protected function cardinality();
    
    public function provideItemEntityMap(): iterable {
        $this->loadMultiEntitiesMap();
        return $this->multiEntitiesMap;        
    }
    
    private function addNewEntity() {
        $cadinality = $this->cardinality();
        switch ($cadinality) {
            case FamilyInterface::CARDINALITY_0_1:
                // exc
            case FamilyInterface::CARDINALITY_1_1:
                if ($this->isMultiEditable() && count($this->multiEntitiesMap)<1) {   // přidat jen pokud adresa není
                    $this->multiEntitiesMap[] = $this->newMultiEntity();
                }
                break;
            case FamilyInterface::CARDINALITY_0_N:
                // exc
            case FamilyInterface::CARDINALITY_1_N:
                    $this->multiEntitiesMap[] = $this->newMultiEntity();                
                break;            
        }
    }
    
}
