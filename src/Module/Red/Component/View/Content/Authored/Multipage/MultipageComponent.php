<?php
namespace Red\Component\View\Content\Authored\Multipage;

use Site\ConfigurationCache;

use Configuration\ComponentConfigurationInterface;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\ImplodeTemplate;
use Pes\View\CompositeView;
use Pes\Text\FriendlyUrl;

use Red\Component\View\Content\Authored\AuthoredComponentAbstract;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageViewModelInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Service\ItemApi\ItemApiServiceInterface;
use Red\Service\CascadeLoader\CascadeLoaderFactoryInterface;

use Access\Enum\AccessPresentationEnum;

/**
 * Description of PaperComponent
 *
 * @author pes2704
 */
class MultipageComponent extends AuthoredComponentAbstract implements MultipageComponentInterface {

    // hodnoty těchto konstant určují, jaká budou jména proměnných genrovaných template rendererem při renderování php template
    // - např, hodnota const QQQ='nazdar' způsobí, že obsah bude v proměnné $nazdar
    const CONTENT = 'content';

    /**
     * @var MultipageViewModelInterface
     */
    protected $contextData;
    
    /**
     * @var ItemApiServiceInterface
     */
    private $itemApiService;
    
    /**
     * @var CascadeLoaderFactoryInterface
     */
    private $cascadeLoaderFactory;
    
    public function __construct(
            ComponentConfigurationInterface $configuration,
            ItemApiServiceInterface $itemApiService, 
            CascadeLoaderFactoryInterface $cascadeLoaderFactory 
            ) {
        parent::__construct($configuration);
        $this->itemApiService = $itemApiService;
        $this->cascadeLoaderFactory = $cascadeLoaderFactory;
    }
    
    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template a použije ji.
     * Pokud soubor template neexistuje, použije ImplodeRenderer (ten zřetězí obsahy jednotlivých komponentních view).
     *
     */
    public function beforeRenderingHook(): void {
        $subNodes = $this->contextData->getSubTree();  //včetně kořene podstromu - tedy včetně multipage položky
        // odstraní kořenový uzel, tj. uzel odpovídající vlastní multipage, zbydou jen potomci
        if (count($subNodes)>1) {
            array_shift($subNodes);   //odstraní první prvek s indexem [0] a výsledné pole opět začína prvkem s indexem [0]
                $contentView = $this->getComponentView(self::CONTENT);
            foreach ($subNodes as $subNode) {
                $menuItem = $subNode->getMenuItem();

                $contentView->appendComponentView(
                        $this->getMenuItemLoader($menuItem),
                        $menuItem->getApiModuleFk().'_'.$menuItem->getId()
                    );
            }
        }
    }

//    public function getString() {
//        return parent::getString();
//    }

#
#### view s content loaderem #####################################################
#

    /**
     * Vrací view s šablonou obsahující skript pro načtení obsahu na základě typu menuItem a id menu item. Načtení probíhá pomocí cascade.js.
     * cascade.js odešle request, získaným obsahem a zamění původní obsah html elementu v layoutu.
     * Parametry uri v načítacím skriptu jsou typ menuItem a id menu item, aby nebylo třeba načítat data s obsahem (paper, article, multipage a další) zde v kontroleru.
     * Pro případ obsahu typu 'static' jsou jako prametry uri předány typ 'static' a jméno statické stránky, které je pak použito pro načtení statické šablony.
     *
     * @param type $menuItem
     * @return View
     */
    private function getMenuItemLoader(MenuItemInterface $menuItem) {
        $dataRedApiUri = $this->itemApiService->getContentApiUri($menuItem);
        return $this->getRedLoadScript($dataRedApiUri);
    }
    
    private function getRedLoadScript($dataRedApiUri) {
        return $this->cascadeLoaderFactory->getRedLoaderElement($dataRedApiUri, $this->contextData->isPartInEditableMode());        
    }

}
