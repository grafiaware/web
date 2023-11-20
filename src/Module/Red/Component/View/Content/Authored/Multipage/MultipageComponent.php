<?php
namespace Red\Component\View\Content\Authored\Multipage;

use Site\ConfigurationCache;

use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\ImplodeTemplate;
use Pes\View\CompositeView;
use Pes\Text\FriendlyUrl;

use Red\Component\View\Content\Authored\AuthoredComponentAbstract;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageViewModelInterface;
use Red\Model\Entity\MenuItemInterface;

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
     *
     * @var MultipageViewModelInterface
     */
    protected $contextData;

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
                        $menuItem->getTypeFk().'_'.$menuItem->getId()
                    );
            }
        }
    }

    public function getString() {
        return parent::getString();
    }

    ### load scripts ###

    /**
     * Vrací view s šablonou obsahující skript pro načtení obsahu na základě typu menuItem a id menu item. Načtení probíhá pomocí cascade.js.
     * cascade.js odešle request a získá obsah a zámění původní obsah html elementu v layoutu.
     * Parametry uri v načítacím skriptu jsou typ menuItem a id menu item, aby nebylo třeba načítat data s obsahem (paper, article, multipage a další) zde v kontroleru.
     * Pro případ obsahu typu 'static' jsou jako prametry uri předány typ 'static' a jméno statické stránky, které je pak použito pro načtení statické šablony.
     *
     * @param type $menuItem
     * @return View
     */
    private function getMenuItemLoader(MenuItemInterface $menuItem) {
        if ($this->contextData->isPartInEditableMode()) {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheReloadOnNav'];
        } else {
            $dataRedCacheControl = ConfigurationCache::layoutController()['cascade.cacheLoadOnce'];
        }

        $menuItemType = $menuItem->getTypeFk();
        switch ($menuItemType) {
            case null:
                $id = $menuItem->getId();
                $componentType = "red/v1/empty";
                break;
            case 'red_static':
                $id = $this->getNameForStaticPage($menuItem);
                $componentType = "red/v1/static";
                break;
            case 'events_static':
                $id = $this->getNameForStaticPage($menuItem);
                $componentType = "events/v1/static";
                break;
            case 'auth_static':
                $id = $this->getNameForStaticPage($menuItem);
                $componentType = "auth/v1/static";
                break;
            default:
                $id = $menuItem->getId();
                $componentType = "red/v1/$menuItemType";
                break;
        }        
        $view = $this->getRedLoadScript($componentType, $id, $dataRedCacheControl);
        return $view;
    }

    private function getNameForStaticPage(MenuItemInterface $menuItem) {
        $menuItemPrettyUri = $menuItem->getPrettyuri();
        if (isset($menuItemPrettyUri) AND $menuItemPrettyUri AND strpos($menuItemPrettyUri, "folded:")===0) {      // EditItemController - line 93
            $name = str_replace('/', '_', str_replace("folded:", "", $menuItemPrettyUri));  // zahodí prefix a nahradí '/' za '_' - recipročně
        } else {
            $name = FriendlyUrl::friendlyUrlText($menuItem->getTitle());
        }
        return $name;
    }
    private function getRedLoadScript($componentType, $componentId, $dataRedCacheControl) {
        /** @var View $view */
//        $view = $this->container->get(View::class);
$view = new CompositeView();
$view->setRendererContainer($this->rendererContainer);

        // prvek data 'loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $uniquid = uniqid();
        $dataRedApiUri = "$componentType/$componentId";

        $view->setData([
                        'class' => ConfigurationCache::layoutController()['cascade.class'],
                        'dataRedCacheControl' => $dataRedCacheControl,
                        'loaderElementId' => "red_loaded_$uniquid",
                        'dataRedApiUri' => $dataRedApiUri,
                        ]);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['templates.loaderElement']));
        return $view;
    }

}
