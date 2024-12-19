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
abstract class ComponentItemAbstract extends CollectionView implements ComponentPrototypeInterface, ComponentItemInterface {

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
    
    private $itemTemplateName;
    private $editableItemTemplateName;


    private $pluginTemplatePath = [];

    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }
        public function __clone() {
        $this->itemViewModel = clone $this->itemViewModel;
    }
    public function setItemViewModel(ViewModelItemInterface $itemViewModel) {
        $this->itemViewModel = $itemViewModel;
    }
    
    public function getItemViewModel(): ViewModelItemInterface {
        return $this->itemViewModel;
    }

    public function setItemTemplate(FileTemplateInterface $template) {
        $this->itemTemplate = $template;
    }
    
    public function setItemTemplatePath($templateFilePath, $editableTemplateFilePath = null) {
        $this->itemTemplateName = $templateFilePath;
        $this->editableItemTemplateName = $editableTemplateFilePath;
    }
    
    public function addPluginTemplatePath($name, $templateFilePath, $editableTemplateFilePath = null) {
        $this->pluginTemplatePath[$name] = ["default"=>$templateFilePath, "editable"=>$editableTemplateFilePath];
    }   
    
    /**
     * Přidí do dat před renderování pole nastavené metodou addPluginTemplateName()
     * 
     * @return void
     */
    public function beforeRenderingHook(): void {
        $editable = $this->itemViewModel->isItemEditable();
        if (isset($this->pluginTemplatePath)) {
            foreach ($this->pluginTemplatePath as $name => $paths) {
                $this->itemViewModel->appendData([$name=>($editable ? $paths["editable"] : $paths["default"])]);                            
            }
        }
        if (isset($this->itemTemplate) AND $this->itemTemplateName) {
            //když není editable, použije se vždy default
            $this->itemTemplate->setTemplateFilename(($editable && $this->editableItemTemplateName) ? $this->editableItemTemplateName : $this->itemTemplateName);
        } else {
            throw new LogicException("ComponentItem musí mít před renderováním nastavenu itemTemplate a alespoň první itemTemplatePath.");
        }
        $this->setTemplate($this->itemTemplate);
        $this->setData($this->itemViewModel);
    }

}
