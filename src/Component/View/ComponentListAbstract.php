<?php
namespace Component\View;

use Pes\View\CollectionView;
use Pes\View\ViewInterface;
use Component\View\ComponentPrototypeInterface;

use Component\View\ComponentListInterface;
use Component\ViewModel\ViewModelListInterface;
use Component\ViewModel\ViewModelFamilyItemInterface;

use Configuration\ComponentConfigurationInterface;

use LogicException;

/**
 * Description of ComponentListAbstract
 *
 * @author pes2704
 */
abstract class ComponentListAbstract extends CollectionView implements ComponentListInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;
    
    protected $viewPrototype;
    
    /**
     * 
     * @var ViewModelListInterface
     */
    private $listViewModel;

    public function __construct(ComponentConfigurationInterface $configuration, ComponentPrototypeInterface $viewPrototype) {
        $this->configuration = $configuration;
        $this->viewPrototype = $viewPrototype;
    }
    
    public function setListViewModel(ViewModelListInterface $listViewModel) {
        $this->listViewModel = $listViewModel;
    }
    
    public function getListViewModel(): ViewModelListInterface {
        return $this->listViewModel;
    }   
    
    /**
     * Získá kolekci dat z view modelu collection komponentu metodou provideDataCollection(). 
     * Tuto data kolekci iteruje (foreach) a pro každá v iteraci získaná data vytvoří nový view klonováním prototypu a tomuto view nastavi získaná data.
     * 
     * @return void
     */
    public function beforeRenderingHook(): void {
        if(!isset($this->listViewModel)) {
            throw new LogicException("Komponent list musí mít nastaven list view model metodou ->setListViewModel(ViewModelListInterface)");
        }
        $componentViewCollection = [];
        foreach ($this->listViewModel->provideItemEntityCollection() as $entity) {
            /** @var ComponentPrototypeInterface $itemComponent */
            $itemComponent = clone ($this->viewPrototype);
            $itemComponent->getItemViewModel()->receiveEntity($entity);
            $componentViewCollection[] = $itemComponent;
        }
        
        $this->appendComponentViewCollection($componentViewCollection);
        $this->setData($this->listViewModel);

    }
}
