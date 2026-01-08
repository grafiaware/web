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
    
    protected $query = [];


    /**
     * Metoda musí načíst z databáze všchny entity pro list a vložit je do pole $this->listEntities
     */
    abstract protected function loadListEntities();  //TODO: SV QUERY set a get query se volá v loadListEntities()

    public function setQuery(array $query): void {  //TODO: SV QUERY
        $this->query = $query;
    }
    
    public function getQuery(): array {  //TODO: SV QUERY
        return $this->query;
    }    
}
