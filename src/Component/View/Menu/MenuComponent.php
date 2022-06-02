<?php

namespace Component\View\Menu;

use Pes\View\CollectionViewInterface;

use Component\View\ComponentCompositeAbstract;
use Component\ViewModel\Menu\MenuViewModelInterface;
use Component\ViewModel\Menu\Item\ItemViewModel;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Component\Renderer\Html\Menu\MenuWrapRendererInterface;
use Component\View\Menu\LevelComponent;
use Component\View\Menu\LevelComponentInterface;
use Component\View\Menu\ItemButtonsComponent;
use Component\View\Menu\ItemComponent;
use Component\View\Menu\ItemComponentInterface;

use Component\View\Manage\ButtonsItemManipulationComponent;
use Component\View\Manage\ButtonsMenuManipulationComponent;
use Component\View\Manage\ButtonsPasteComponent;

use Access\Enum\AccessPresentationEnum;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends ComponentCompositeAbstract implements MenuComponentInterface {

    /**
     * @var MenuViewModelInterface
     */
    protected $contextData;

    private $menuWrapRendererName;
    private $levelWrapRendererName;
    private $itemRendererName;
    private $itemEditableRendererName;

    private $withTitle = false;

    private $rootRealDepth;
    private $editableMode;

    public function getString() {
        $str = parent::getString();
        return $str;
    }

    /**
     *
     * @param $levelWrapRendererName
     * @return MenuComponentInterface
     */
    public function setRenderersNames($menuWrapRendererName, $levelWrapRendererName, $itemRendererName, $itemEditableRendererName): MenuComponentInterface {
        $this->menuWrapRendererName = $menuWrapRendererName;
        $this->levelWrapRendererName = $levelWrapRendererName;
        $this->itemRendererName = $itemRendererName;
        $this->itemEditableRendererName = $itemEditableRendererName;
        return $this;
    }

    /**
     *
     * @param string $menuRootName
     * @return MenuComponentInterface
     */
    public function setMenuRootName($menuRootName): MenuComponentInterface {
        $this->contextData->setMenuRootName($menuRootName);
        return $this;
    }

    /**
     *
     * @param bool $withTitle
     * @return MenuComponentInterface
     */
    public function withTitleItem($withTitle=false): MenuComponentInterface {
        $this->withTitle = $withTitle;
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
                    $itemViewModelStack[$itemDepth][] = $itemViewModel;
                    $currDepth = $itemDepth;
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
        $levelComponent = new LevelComponent($this->configuration);
        if ($targetDepth==$this->rootRealDepth) {
            $levelComponent->setRendererName($this->menuWrapRendererName);
        } else {
            $levelComponent->setRendererName($this->levelWrapRendererName);
        }
        $levelComponent->setRendererContainer($this->rendererContainer);
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
            if($this->editableMode) {
                $item->setData($itemViewModel)->setRendererName($this->itemEditableRendererName)->setRendererContainer($this->rendererContainer);
                $itemButtons = new ItemButtonsComponent($this->configuration);  // typu InheritData - dědí ItemViewModel
                #### buttons ####
                if ($itemViewModel->isPasteMode()) {
                    $buttonComponents[] = new ButtonsPasteComponent($this->configuration);  // typu InheritData - dědí ItemViewModel
                } else {
                    $buttonComponents[] = new ButtonsItemManipulationComponent($this->configuration);  // typu InheritData - dědí ItemViewModel
                    $buttonComponents[] = new ButtonsMenuManipulationComponent($this->configuration);  // typu InheritData - dědí ItemViewModel
                }
                $this->setRendering($buttonComponents);
                $itemButtons->appendComponentViewCollection($buttonComponents);
                $item->appendComponentView($itemButtons, ItemComponentInterface::ITEM_BUTTONS);
            } else {
                $item->setData($itemViewModel)->setRendererName($this->itemRendererName)->setRendererContainer($this->rendererContainer);
            }
            $nextLevel = $itemViewModel->getChild();
            if (isset($nextLevel)) {
                $item->appendComponentView($nextLevel, ItemComponentInterface::LEVEL);
            }
            $items[] = $item;
        }
        return $items;
    }

    private function setRendering(array $buttons) {
        foreach ($buttons as $button) {
            /** @var CollectionViewInterface $button */
            $button->setRendererContainer($this->rendererContainer);
            $button->setRendererName($this->itemRendererName);
        }
    }
}
