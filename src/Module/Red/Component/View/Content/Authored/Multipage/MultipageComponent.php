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
            foreach ($subNodes as $subNode) {
                $contentView = $this->getComponentView(self::CONTENT);
                $item = $subNode->getMenuItem();
                $contentView->appendComponentView($this->getContentLoadScript($item), $item->getTypeFk().'_'.$item->getId());
                $pages[] = $this->getContentLoadScript($item);
            }
            $this->contextData->offsetSet('pages', $pages);
        }
    }

    public function getString() {
        return parent::getString();
    }

    ### load scripts ###

    protected function getContentLoadScript($menuItem) {
        /** @var View $view */
        $view = new CompositeView();
        $view->setRendererContainer($this->rendererContainer);
//        $view = $this->container->get(View::class);   // v Layout kontroleru mám kontejner

        // prvek data 'loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $uniquid = uniqid();
        $menuItemType = $menuItem->getTypeFk();
        if (!isset($menuItemType)) {
            $menuItemType = 'empty';
        }
        switch ($menuItemType) {
            case 'static':
                $id = $this->getNameForStaticPage($menuItem);
                $dataRedApiUri = "red/v1/$menuItemType/$id";
                break;
            case 'eventcontent':
                $id = $this->getNameForStaticPage($menuItem);
                $dataRedApiUri = "events/v1/$menuItemType/$id";
                break;
            default:
                $id = $menuItem->getId();
                $dataRedApiUri = "red/v1/$menuItemType/$id";
                break;
        }
        $view->setData([
                        'loaderElementId' => "red_loaded_$uniquid",
                        'dataRedApiUri' => $dataRedApiUri,
                        'dataRedInfo' => "{$menuItemType}_for_item_{$id}",
                        'dataRedSelector' => $menuItem->getUidFk()
                        ]);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['templates.loaderElement']));
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

}