<?php
namespace Component\View;

use Pes\View\CollectionView;
use Pes\View\ViewInterface;
use Component\View\ComponentPrototypeInterface;
use Pes\View\Template\FileTemplateInterface;
use Component\View\ComponentItemInterface;
use Component\ViewModel\ViewModelItemInterface;

use Configuration\ComponentConfigurationInterface;

use Pes\Type\ContextDataInterface;

use LogicException;

/**
 * Description of ComponentItemAbstract
 *
 * @author pes2704
 */
abstract class ComponentItemAbstract extends CollectionView implements ComponentPrototypeInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;
    
    /**
     * 
     * @var FileTemplateInterface
     */
    private $itemTemplate;
    
    /**
     * View model je stavobý objekt, je nutné jej klonovat - musí být protected pro metodu __clone()
     * @var ViewModelItemInterface
     */
    protected $itemViewModel;
    
    private $itemTemplatePath;
    private $editableItemTemplatePath;


    private $pluginTemplatePath = [];

    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }
    
    /**
     * {@inheritDoc}
     * 
     */
    public function __clone() {
        $this->itemViewModel = clone $this->itemViewModel;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param ViewModelItemInterface $itemViewModel
     */
    public function setItemViewModel(ViewModelItemInterface $itemViewModel) {
        $this->itemViewModel = $itemViewModel;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return ViewModelItemInterface
     * @throws LogicException
     */
    public function getItemViewModel(): ViewModelItemInterface {
        if (!isset($this->itemViewModel)) {
            $cls = get_called_class();
            throw new LogicException("Komponent $cls nemá nastaven ItemViewModel. ");
        }
        
        return $this->itemViewModel;
    }

    /**
     * {@inheritDoc}
     * 
     * @param FileTemplateInterface $template
     */
    public function setItemTemplate(FileTemplateInterface $template) {
        $this->itemTemplate = $template;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param type $templateFilePath
     * @param type $editableTemplateFilePath
     */
    public function setItemTemplatePath($templateFilePath, $editableTemplateFilePath = null) {
        $this->itemTemplatePath = $templateFilePath;
        $this->editableItemTemplatePath = $editableTemplateFilePath;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param type $name
     * @param type $templateFilePath
     * @param type $editableTemplateFilePath
     */
    public function addPluginTemplatePath($name, $templateFilePath, $editableTemplateFilePath = null) {
        $this->pluginTemplatePath[$name] = ["default"=>$templateFilePath, "editable"=>$editableTemplateFilePath];
    }   
    
    /**
     * Před renderováním přidá do dat pole template path pro pluginy nastavené metodou addPluginTemplatePath()
     * Pokud není zadán renderer, nastaví objektu item template (zadaném metodou setItemTemplate() ) správnou template path - editační template nebo needitační template=default (nastavené metodou setItemTemplatePath() ).
     * @return void
     */
    public function beforeRenderingHook(): void {
        $editableData = $this->itemViewModel->isDataEditable();
        if (isset($this->pluginTemplatePath)) {
            foreach ($this->pluginTemplatePath as $name => $paths) {
                $this->itemViewModel->appendData([$name=>($editableData ? $paths["editable"] : $paths["default"])]);                            
            }
        }
        if(!isset($this->renderer) && !isset($this->rendererName)) {        // např. NoContentRenderer
            if (isset($this->itemTemplate) AND $this->itemTemplatePath) {
                //když není editable, použije se vždy default
                $this->itemTemplate->setTemplateFilename(($editableData && $this->editableItemTemplatePath) ? $this->editableItemTemplatePath : $this->itemTemplatePath);
            } else {
                throw new LogicException("ComponentItem musí mít před renderováním nastavenu itemTemplate a alespoň první itemTemplatePath.");
            }
            $this->setTemplate($this->itemTemplate);
        }
        $this->setData($this->itemViewModel);
    }

}
