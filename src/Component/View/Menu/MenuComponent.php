<?php

namespace Component\View\Menu;

use Pes\View\CollectionViewInterface;
use Psr\Container\ContainerInterface;

use Component\View\ComponentCompositeAbstract;

use Configuration\ComponentConfigurationInterface;

use Component\ViewModel\Menu\MenuViewModelInterface;
use Component\ViewModel\Menu\LevelViewModelInterface;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Component\View\Menu\LevelComponent;
use Component\View\Menu\LevelComponentInterface;
use Component\View\Menu\ItemButtonsComponent;
use Component\View\Menu\ItemComponent;
use Component\View\Menu\ItemComponentInterface;

use Component\View\Manage\ButtonsMenuItemManipulationComponent;
use Component\View\Manage\ButtonsMenuAddComponent;
use Component\View\Manage\ButtonsMenuAddOnelevelComponent;
use Component\View\Manage\ButtonsMenuCutCopyComponent;
use Component\View\Manage\ButtonsMenuCutCopyEscapeComponent;
use Component\View\Manage\ButtonsMenuDeleteComponent;

use Component\Renderer\Html\Manage\ButtonsItemManipulationRenderer;
use Component\Renderer\Html\Manage\ButtonsMenuAddMultilevelRenderer;
use Component\Renderer\Html\Manage\ButtonsMenuAddOnelevelRenderer;
use Component\Renderer\Html\Manage\ButtonsMenuPasteOnelevelRenderer;
use Component\Renderer\Html\Manage\ButtonsMenuPasteMultilevelRenderer;
use Component\Renderer\Html\Manage\ButtonsMenuCutCopyRenderer;
use Component\Renderer\Html\Manage\ButtonsMenuCutCopyEscapeRenderer;
use Component\Renderer\Html\Manage\ButtonsMenuDeleteRenderer;

use Access\Enum\AccessPresentationEnum;
// pro metodu -> do contejneru
use Access\AccessPresentation;
use Pes\View\ViewInterface;
use Component\Renderer\Html\NoPermittedContentRenderer;

use Component\ViewModel\Menu\Enum\ItemTypeEnum;

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
     * Nastaví renderery z kontejneru podle jmen zadaných setRendererName() a setRendererNames(), nastaví parametry menu.
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
        $this->contextData->setMaxDepth(null);
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
                    if ($itemDepth-$currDepth==1) {
                        $itemViewModelStack[$itemDepth][] = $itemViewModel;
                        $currDepth = $itemDepth;
                    } else {
                        //TODO: log nebo např. render span s hlášením
//                        $uidWithNonactiveParent = $itemViewModel->getHierarchyAggregate()->getUid();
//                        throw new LogicException( "Poškozené menu - neaktivní položka má aktivní potomky. Neaktivní nadřízená položka nad položkou s uid '$uidWithNonactiveParent'.");
                    }
                } elseif ($itemDepth<$currDepth) {
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
            end($itemViewModelStack[$i-1])->setChild($levelComponent);
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
            if ($itemViewModel->isPresented()) {
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
