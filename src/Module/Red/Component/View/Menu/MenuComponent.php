<?php

namespace Red\Component\View\Menu;

use Pes\View\CollectionViewInterface;
use Psr\Container\ContainerInterface;

use Red\Component\View\ComponentCompositeAbstract;

use Configuration\ComponentConfigurationInterface;

use Red\Component\ViewModel\Menu\MenuViewModelInterface;
use Red\Component\ViewModel\Menu\LevelViewModelInterface;
use Red\Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Red\Component\View\Menu\LevelComponent;
use Red\Component\View\Menu\LevelComponentInterface;
use Red\Component\View\Menu\ItemButtonsComponent;
use Red\Component\View\Menu\ItemComponent;
use Red\Component\View\Menu\ItemComponentInterface;

use Red\Component\View\Manage\ButtonsMenuItemManipulationComponent;
use Red\Component\View\Manage\ButtonsMenuAddComponent;
use Red\Component\View\Manage\ButtonsMenuAddOnelevelComponent;
use Red\Component\View\Manage\ButtonsMenuCutCopyComponent;
use Red\Component\View\Manage\ButtonsMenuCutCopyEscapeComponent;
use Red\Component\View\Manage\ButtonsMenuDeleteComponent;

use Red\Component\Renderer\Html\Manage\ButtonsItemManipulationRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuAddMultilevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuAddOnelevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuPasteOnelevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuPasteMultilevelRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuCutCopyRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuCutCopyEscapeRenderer;
use Red\Component\Renderer\Html\Manage\ButtonsMenuDeleteRenderer;

use Access\Enum\AccessPresentationEnum;
// pro metodu -> do contejneru
use Access\AccessPresentation;
use Pes\View\ViewInterface;
use Red\Component\Renderer\Html\NoPermittedContentRenderer;

use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;

use LogicException;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends ComponentCompositeAbstract implements MenuComponentInterface {

    private $container;

    /**
     * @var MenuViewModelInterface
     */
    protected $contextData;

    private $levelRendererName;
    private $itemRendererName;
    private $itemEditableRendererName;

    private $rootRealDepth;
    private $editableMode;

    public function __construct(ComponentConfigurationInterface $configuration, ContainerInterface $container) {
        parent::__construct($configuration);
        $this->container = $container;
    }

    public function getString() {
        $str = parent::getString();
        return $str;
    }

    /**
     *
     * @param $levelRendererName
     * @return MenuComponentInterface
     */
    public function setRenderersNames($levelRendererName, $itemRendererName, $itemEditableRendererName): MenuComponentInterface {
        $this->levelRendererName = $levelRendererName;
        $this->itemRendererName = $itemRendererName;
        $this->itemEditableRendererName = $itemEditableRendererName;
        return $this;
    }

    /**
     * Z pole ItemModelů vygeneruje "strom" komponentů - menu komponentě přidá kompozitní level komponenty, předtím jednotlivým level komponentám přidá komponentní item komponenty,
     * atd.
     * Výsledkem je tato komponenta s kompozitními komponentami (view) připravená na renderování.
     *
     * @return void
     * @throws \LogicException
     */
    public function beforeRenderingHook(): void {
        // set renderer
//        if (!isset($this->rendererContainer)) {
//            throw new \LogicException("Komponent ".get_called_class()." nemá nastaven renderer kontejner metodou setRendererContainer().");
//        }
//        /** @var MenuWrapRendererInterface $menuWrapRenderer */
//        $menuWrapRenderer = $this->rendererContainer->get($this->rendererName);
//        $menuWrapRenderer->setLevelWrapRenderer($this->rendererContainer->get($this->levelWrapRendererName));
//        $this->setRenderer($menuWrapRenderer);

        // minimální hloubka u menu bez zobrazení kořenového prvku je 2 (pro 1 je nodes pole v modelu prázdné), u menu se zobrazením kořenového prvku je minimálmí hloubka 1, ale nodes pak obsahuje jen kořenový prvek
        $this->editableMode = $this->contextData->presentEditableMenu();

        $subtreeItemModels = $this->contextData->getItemModels();

        if ($subtreeItemModels) {
            $level = '';
            $itemTags = [];
            $first = true;
            foreach ($subtreeItemModels as $itemViewModel) {
                /** @var ItemViewModelInterface $itemViewModel */
                $itemDepth = $itemViewModel->getRealDepth();
                if ($first) {
                    $this->rootRealDepth = $itemDepth;
                    $currDepth = $itemDepth;
                    $first = false;
                }
                if ($itemDepth>$currDepth) {
//                    if ($itemDepth-$currDepth==1) {
                        $itemViewModelStack[$itemDepth][] = $itemViewModel;
                        $currDepth = $itemDepth;
//                    } else {
//                        //TODO: log nebo např. render span s hlášením
//                        $uidWithNonactiveParent = $itemViewModel->getHierarchyAggregate()->getUid();
////                        throw new LogicException( "Poškozené menu - neaktivní položka má aktivní potomky. Neaktivní nadřízená položka nad položkou s uid '$uidWithNonactiveParent'.");
//                    }
                } elseif ($itemDepth<$currDepth) {
                    // následující dva řádky jsou přehozené
                    $this->createChildrenComponents($currDepth, $itemDepth, $itemViewModelStack);
                    $itemViewModelStack[$itemDepth][] = $itemViewModel;
                    $currDepth = $itemDepth;
                } else {
                    $itemViewModelStack[$currDepth][] = $itemViewModel;
                }
            }
            $this->createChildrenComponents($currDepth, $this->rootRealDepth, $itemViewModelStack);
            $this->createLastLevelChildrenComponents($itemViewModelStack);
        }
    }

    private function createChildrenComponents($currDepth, $targetDepth, &$itemViewModelStack) {
        for ($i=$currDepth; $i>$targetDepth; $i--) {
            $itemComponents = $this->createItemComponents($itemViewModelStack[$i]);
            $levelComponent = $this->createLevelComponent($targetDepth, $itemComponents);
            unset($itemViewModelStack[$i]);
            if (!isset($itemViewModelStack[$i-1])) {
                $stop = true;
            } else {
                end($itemViewModelStack[$i-1])->hydrateChild($levelComponent);
            }
        }
    }

    private function createLastLevelChildrenComponents(&$itemViewModelStack) {
            $itemComponents = $this->createItemComponents($itemViewModelStack[$this->rootRealDepth]);
            $levelComponent = $this->createLevelComponent($this->rootRealDepth, $itemComponents);
            $this->appendComponentView($levelComponent, MenuComponentInterface::MENU);
    }

    private function createLevelComponent($targetDepth, $itemComponents) {
        /** @var LevelComponentInterface $levelComponent */
        $levelComponent = $this->container->get(LevelComponent::class);
        $levelViewModel = $levelComponent->getData();
        /** @var LevelViewModelInterface $levelViewModel */
        $levelViewModel->setLastLevel($targetDepth==$this->rootRealDepth);
        $levelComponent->setRendererName($this->levelRendererName);
        $levelComponent->appendComponentViewCollection($itemComponents);
        return $levelComponent;
    }

//    private function addLastLevelItemComponents(&$itemViewModelStackLevel, $editableMode): void {
//        $items = $this->createItemComponents($itemViewModelStackLevel, $editableMode);
//        $this->appendComponentViewCollection($items);
//    }

    private function createItemComponents($itemViewModelStackLevel): array {
        $items = [];
        foreach ($itemViewModelStackLevel as $itemViewModel) {
            /** @var ItemViewModelInterface $itemViewModel */
            $item = new ItemComponent($this->configuration);
            $item->setData($itemViewModel)->setRendererContainer($this->rendererContainer);
            if($this->editableMode) {
            $item->setRendererName($this->itemEditableRendererName);
            // u kořenového item menu nejsou buttony
            if ($itemViewModel->isPresented() AND !$itemViewModel->isRoot()) {
                $itemButtons = $this->createItemButtonsComponent();
                $item->appendComponentView($itemButtons, ItemComponentInterface::ITEM_BUTTONS);
            }
            } else {
                $item->setRendererName($this->itemRendererName);
            }
            $nextLevel = $itemViewModel->getChild();
            if (isset($nextLevel)) {
                $item->appendComponentView($nextLevel, ItemComponentInterface::LEVEL);
            }
            $items[] = $item;
        }
        return $items;
    }

    private function createItemButtonsComponent(): ItemButtonsComponent {
        $itemButtons = new ItemButtonsComponent($this->configuration);  // typu InheritData - dědí ItemViewModel
        $cut = $this->contextData->getPostCommand('cut') ? true : false;
        $copy = $this->contextData->getPostCommand('copy') ? true :false;
        $pasteMode = ($cut OR $copy);

        #### buttons ####
        $buttonComponents = [];
        switch ((new ItemTypeEnum())($this->contextData->getItemType())) {
            // ButtonsXXX komponenty jsou typu InheritData - dědí ItemViewModel
            case ItemTypeEnum::MULTILEVEL:
                $buttonComponents[] = $this->createItemManipulationButtons();
                $buttonComponents[] = $this->createAddOrPasteMultilevelButtons($pasteMode);
                $buttonComponents[] = $this->createCutCopyButtons($pasteMode);
                break;
            case ItemTypeEnum::ONELEVEL:
                $buttonComponents[] = $this->createItemManipulationButtons();
                $buttonComponents[] = $this->createAddOrPasteOnelevelButtons($pasteMode);
                $buttonComponents[] = $this->createCutCopyButtons($pasteMode);
                break;
            case ItemTypeEnum::TRASH:
                $buttonComponents[] = $this->createCutCopyButtons($pasteMode);
                $buttonComponents[] = $this->createDeleteButtons();
                break;
            default:
                throw new LogicException("Nerozpoznán typ položek menu (hodnota vrácená metodou viewModel->getItemType())");
                break;
        }
        $itemButtons->appendComponentViewCollection($buttonComponents);
        return $itemButtons;
    }

    private function createItemManipulationButtons() {
        return $this->setRenderingByAccess(new ButtonsMenuItemManipulationComponent($this->configuration), ButtonsItemManipulationRenderer::class);
    }

    private function createDeleteButtons() {
        return $this->setRenderingByAccess(new ButtonsMenuDeleteComponent($this->configuration), ButtonsMenuDeleteRenderer::class);
    }

    private function createCutCopyButtons($pasteMode) {
        if ($pasteMode) {
            return $this->setRenderingByAccess(new ButtonsMenuCutCopyComponent($this->configuration), ButtonsMenuCutCopyEscapeRenderer::class);
        } else {
            return $this->setRenderingByAccess(new ButtonsMenuCutCopyComponent($this->configuration), ButtonsMenuCutCopyRenderer::class);
        }
    }

    private function createAddOrPasteOnelevelButtons($pasteMode) {
        if ($pasteMode) {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuPasteOnelevelRenderer::class);
        } else {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuAddOnelevelRenderer::class);
        }
    }

    private function createAddOrPasteMultilevelButtons($pasteMode) {
        if ($pasteMode) {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuPasteMultilevelRenderer::class);
        } else {
            return $this->setRenderingByAccess(new ButtonsMenuAddComponent($this->configuration), ButtonsMenuAddMultilevelRenderer::class);
        }
    }

    private function setRenderingByAccess(ViewInterface $component, $rendererName) {
        /** @var AccessPresentationInterface $accessPresentation */
        $accessPresentation = $this->container->get(AccessPresentation::class);
        if($accessPresentation->isAllowed($component, AccessPresentationEnum::EDIT)) {
            $component->setRendererName($rendererName);
        } else {
            $component->setRendererName(NoPermittedContentRenderer::class);
        }
        $component->setRendererContainer($this->rendererContainer);
        return $component;
    }
}