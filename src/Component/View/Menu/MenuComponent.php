<?php

namespace Component\View\Menu;

use Pes\View\ViewInterface;

use Component\View\StatusComponentAbstract;
use Component\ViewModel\Menu\MenuViewModelInterface;
use Component\ViewModel\Menu\Item\ItemViewModel;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Component\Renderer\Html\Menu\MenuWrapRendererInterface;
use Component\View\Menu\LevelComponent;
use Component\View\Menu\LevelComponentInterface;
use Component\View\Element\ElementComponent;

use Access\Enum\AccessPresentationEnum;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends StatusComponentAbstract implements MenuComponentInterface {

    const TOGGLE_EDIT_MENU_BUTTON = 'toggleEditMenuButton';

    /**
     * @var MenuViewModelInterface
     */
    protected $contextData;

    private $levelWrapRendererName;
    private $itemRendererName;
    private $itemEditableRendererName;

    private $withTitle = false;

    public function getString() {
        $str = parent::getString();
        return $str;
    }

    /**
     *
     * @param $levelWrapRendererName
     * @return MenuComponentInterface
     */
    public function setRenderersNames( $levelWrapRendererName, $itemRendererName, $itemEditableRendererName): MenuComponentInterface {
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
        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Komponent ".get_called_class()." nemá nastaven renderer kontejner metodou setRendererContainer().");
        }
        /** @var MenuWrapRendererInterface $menuWrapRenderer */
        $menuWrapRenderer = $this->rendererContainer->get($this->rendererName);
        $menuWrapRenderer->setLevelWrapRenderer($this->rendererContainer->get($this->levelWrapRendererName));
        $this->setRenderer($menuWrapRenderer);

        // minimální hloubka u menu bez zobrazení kořenového prvku je 2 (pro 1 je nodes pole v modelu prázdné), u menu se zobrazením kořenového prvku je minimálmí hloubka 1, ale nodes pak obsahuje jen kořenový prvek
        $this->contextData->setMaxDepth(null);

        $subtreeItemModels = $this->contextData->getItemModels();
        $editableMode = $this->contextData->presentEditableMenu() AND $this->isAllowedToPresent(AccessPresentationEnum::EDIT);

        if ($subtreeItemModels) {
            $level = '';
            $itemTags = [];
            $first = true;
            foreach ($subtreeItemModels as $itemViewModel) {
                /** @var ItemViewModelInterface $itemViewModel */
                $itemDepth = $itemViewModel->getRealDepth();
                if ($first) {
                    $rootDepth = $itemDepth;
                    $currDepth = $itemDepth;
                    $first = false;
                }
                if ($itemDepth>$currDepth) {
                    $itemViewModelStack[$itemDepth][] = $itemViewModel;
                    $currDepth = $itemDepth;
                } elseif ($itemDepth<$currDepth) {
                    $this->createLevelComponent($currDepth, $itemDepth, $itemViewModelStack, $editableMode);
                    $itemViewModelStack[$itemDepth][] = $itemViewModel;
                    $currDepth = $itemDepth;
                } else {
                    $itemViewModelStack[$currDepth][] = $itemViewModel;
                }
            }
            $this->createLevelComponent($currDepth, $rootDepth, $itemViewModelStack, $editableMode);
            $this->addLastLevelItemComponents($itemViewModelStack[$rootDepth], $editableMode);
        }
    }

    private function createLevelComponent($currDepth, $targetDepth, &$itemViewModelStack, $editableMode) {
        for ($i=$currDepth; $i>$targetDepth; $i--) {
            $levelComponent = new LevelComponent($this->configuration);
            $this->appendItemComponents($levelComponent, $itemViewModelStack[$i], $editableMode);
            unset($itemViewModelStack[$i]);
            end($itemViewModelStack[$i-1])->setChild($levelComponent);
        }
    }

    private function addLastLevelItemComponents(&$itemViewModelStackLevel, $editableMode): void {
        $this->appendItemComponents($this, $itemViewModelStackLevel, $editableMode);
    }

    private function appendItemComponents(ViewInterface $component, $itemViewModelStackLevel, $editableMode) {
        $component->setRendererName($this->levelWrapRendererName);
        $component->setRendererContainer($this->rendererContainer);
        foreach ($itemViewModelStackLevel as $itemViewModel) {
            /** @var ItemViewModelInterface $itemViewModel */
            if($editableMode) {
                $item = (new ElementComponent($this->configuration))->setData($itemViewModel)->setRendererName($this->itemEditableRendererName)->setRendererContainer($this->rendererContainer);
            } else {
                $item =  (new ElementComponent($this->configuration))->setData($itemViewModel)->setRendererName($this->itemRendererName)->setRendererContainer($this->rendererContainer);
            }
            $nextLevel = $itemViewModel->getChild();
            if (isset($nextLevel)) {
                $item->appendComponentView($nextLevel, uniqid('level')); //'level'.$itemViewModel->getRealDepth());
            }
            $component->appendComponentView($item, uniqid('item'));
        }
    }
}
