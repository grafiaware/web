<?php
namespace Component\View;

use Pes\View\CollectionView;
use Pes\View\ViewInterface;
use Component\View\ComponentPrototypeInterface;

use Component\View\ComponentItemInterface;
use Component\ViewModel\ViewModelItemInterface;

use Configuration\ComponentConfigurationInterface;

use Pes\Type\ContextDataInterface;

use LogicException;

/**
 * Description of ComponentListAbstract
 *
 * @author pes2704
 */
abstract class ComponentItemAbstract extends CollectionView implements ComponentItemInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;
        
    private $itemViewModel;
    
    private $pluginTemplatePath = [];

    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }
    
    public function setItemViewModel(ViewModelItemInterface $itemViewModel) {
        $this->itemViewModel = $itemViewModel;
        $this->setData($itemViewModel);
    }
    
    public function addPluginTemplateName($name, $templateFilePath) {
        $this->pluginTemplatePath[$name] = $templateFilePath;
    }   
    
    /**
     * Přidí do dat před renderování pole nastavené metodou addPluginTemplateName()
     * 
     * @return void
     */
    public function beforeRenderingHook(): void {
        /** @var ContextDataInterface $data */
        $data = $this->getData();
        $data->appendData($this->pluginTemplatePath);
    }

}
