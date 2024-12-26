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
     * @var FileTemplateInterface
     */
    private $multiTemplate;
    private $multiTemplateName;
    private $editableMultiTemplateName;    
    protected $viewPrototype;
    
    /**
     * 
     * @var ViewModelMultiInterface
     */
    private $multiViewModel;

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
        $this->multiTemplateName = $templateFilePath;
        $this->editableMultiTemplateName = $editableTemplateFilePath;
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
              

        if (isset($this->multiTemplate) AND $this->multiTemplateName) {
            //když není editable, použije se vždy default
            $editable = $this->multiViewModel->isMultiEditable();
            $this->multiTemplate->setTemplateFilename(($editable && $this->editableMultiTemplateName) ? $this->editableMultiTemplateName : $this->multiTemplateName);
        } else {
            throw new LogicException("ComponentItem musí mít před renderováním nastavenu itemTemplate a alespoň první itemTemplatePath.");
        }
        $this->setTemplate($this->multiTemplate);
        $this->setData($this->multiViewModel);
        
    }
}
