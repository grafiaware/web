<?php
namespace Component\View;

use Pes\View\CollectionView;
use Pes\View\ViewInterface;
use Component\View\ComponentItemPrototypeInterface;

use Component\View\ComponentListInterface;
use Component\ViewModel\ViewModelCollectionInterface;

use Configuration\ComponentConfigurationInterface;
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
    
    private $viewPrototype;
    
    private $collectionViewModel = [];

    public function __construct(ComponentConfigurationInterface $configuration, ComponentItemPrototypeInterface $viewPrototype) {
        $this->configuration = $configuration;
        $this->viewPrototype = $viewPrototype;
    }
    
    public function setCollectionViewModel(ViewModelCollectionInterface $collectionViewModel) {
        $this->collectionViewModel = $collectionViewModel;
    }
    
    /**
     * Získá kolekci dat z view modelu collection komponentu metodou provideDataCollection(). 
     * Tuto data kolekci iteruje (foreach) a pro každá v iteraci získaná data vytvoří nový view klonováním prototypu a tomuto view nastavi získaná data.
     * 
     * @return void
     */
    public function beforeRenderingHook(): void {
//        $this->getData()->hydrateChildViewModels();   // přidej interface ChildViewModelInterface
        $componentViewCollection = [];
        foreach ($this->collectionViewModel->provideDataCollection() as $componentData) {
            /** @var ViewInterface $view */
            $view = clone ($this->viewPrototype);
            $view->setData($componentData);
            $componentViewCollection[] = $view;
        }
        
        $this->appendComponentViewCollection($componentViewCollection);
    }
    
}
