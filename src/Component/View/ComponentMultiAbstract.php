<?php
namespace Component\View;

use Pes\View\CompositeView;
use Pes\View\ViewInterface;
use Component\View\ComponentPrototypeInterface;

use Component\View\ComponentMultiInterface;
use Component\ViewModel\ViewModelMultiInterface;
use Component\ViewModel\FamilyInterface;

use Configuration\ComponentConfigurationInterface;
use Pes\View\Template\FileTemplateInterface;
use LogicException;

/**
 * Description of ComponentListAbstract
 *
 * @author pes2704
 */
abstract class ComponentMultiAbstract extends CompositeView implements ComponentMultiInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;
    
    /**
     * 
     * @var ViewModelMultiInterface
     */
    private $multiViewModel;
    
    /**
     * 
     * @var FileTemplateInterface
     */
    private $multiTemplate;
    private $multiTemplatePath;
    private $editableMultiTemplatePath;    
    protected $viewPrototype;

    private $pluginTemplatePath = [];

    public function __construct(ComponentConfigurationInterface $configuration, ComponentPrototypeInterface $viewPrototype) {
        $this->configuration = $configuration;
        $this->viewPrototype = $viewPrototype;
    }
    
    public function setMultiViewModel(ViewModelMultiInterface $multiViewModel) {
        $this->multiViewModel = $multiViewModel;
    }
    
    public function getMultiViewModel(): ViewModelMultiInterface {
        return $this->multiViewModel;
    }   
    
    public function setMultiTemplate(FileTemplateInterface $template) {
        $this->multiTemplate = $template;
    }
    
    public function setMultiTemplatePath($templateFilePath, $editableTemplateFilePath = null) {
        $this->multiTemplatePath = $templateFilePath;
        $this->editableMultiTemplatePath = $editableTemplateFilePath;
    }
    
    public function addPluginTemplatePath($name, $templateFilePath, $editableTemplateFilePath = null) {
        $this->pluginTemplatePath[$name] = ["default"=>$templateFilePath, "editable"=>$editableTemplateFilePath];
    }
    
    /**
     * Získá kolekci dat z view modelu collection komponentu metodou provideDataCollection(). 
     * Tuto data kolekci iteruje (foreach) a pro každá v iteraci získaná data vytvoří nový view klonováním prototypu a tomuto view nastavi získaná data.
     * 
     * @return void
     */
    public function beforeRenderingHook(): void {
        if(!isset($this->multiViewModel)) {
            throw new LogicException("Komponent list musí mít nastaven list view model metodou ->setListViewModel(ViewModelListInterface)");
        }
        foreach ($this->multiViewModel->provideItemEntityMap() as $id => $entity) {
            /** @var ComponentPrototypeInterface $itemComponent */
            $itemComponent = clone ($this->viewPrototype);
            $itemComponent->getItemViewModel()->receiveEntity($entity);
            $this->appendComponentView($itemComponent, $id);
        }
              
        $editable = $this->multiViewModel->isMultiEditable();
        if (isset($this->pluginTemplatePath)) {
            foreach ($this->pluginTemplatePath as $name => $paths) {
                $this->multiViewModel->appendData([$name=>($editable ? $paths["editable"] : $paths["default"])]);                            
            }
        }
        if (isset($this->multiTemplate) AND $this->multiTemplatePath) {
            //když není editable, použije se vždy default
            $this->multiTemplate->setTemplateFilename(($editable && $this->editableMultiTemplatePath) ? $this->editableMultiTemplatePath : $this->multiTemplatePath);
        } else {
            throw new LogicException("ComponentItem musí mít před renderováním nastavenu multiTemplate a alespoň první multiTemplatePath.");
        }       
        
        $this->setTemplate($this->multiTemplate);
        $this->setData($this->multiViewModel);
        
    }
}
