<?php
namespace Component\View\MenuItem\Authored\Multipage;

use Site\ConfigurationCache;

use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\ImplodeTemplate;
use Pes\View\CompositeView;
use Pes\Text\FriendlyUrl;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;
use Component\ViewModel\MenuItem\Authored\Multipage\MultipageViewModelInterface;

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
        $subNodes = $this->contextData->getSubNodes();  //včetně kořene podstromu - tedy včetně multipage položky
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
        $menuItemType = $menuItem->getTypeFk();
        if ($menuItemType!='static') {
            $id = $menuItem->getId();
        } else {
            $id = $this->getNameForStaticPage($menuItem);
        }
        // prvek data ''loaderWrapperElementId' musí být unikátní - z jeho hodnoty se generuje id načítaného elementu - a id musí být unikátní jinak dojde k opakovanému přepsání obsahu elemntu v DOM
        $view = (new View())
                    ->setData([
                        'loaderWrapperElementId' => "content_for_item_{$id}_with_type_{$menuItemType}",
                        'apiUri' => "web/v1/$menuItemType/$id"
                        ]);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['templates.loaderElement']));  //TODO: loader element oddělit samostatně
        $view->setRendererContainer($this->rendererContainer);
        return $view;
    }

    private function getNameForStaticPage(MenuItemInterface $menuItem) {
        $menuItemPrettyUri = $menuItem->getPrettyuri();
        if (isset($menuItemPrettyUri) AND $menuItemPrettyUri AND strpos($menuItemPrettyUri, "folded:")===0) {      // EditItemController - line 93
            $name = str_replace('/', '_', str_replace("folded:", "", $menuItemPrettyUri));  // zahodí prefix a nahradí '/' za '_' - recopročně
        } else {
            $name = FriendlyUrl::friendlyUrlText($menuItem->getTitle());
        }
        return $name;
    }

}