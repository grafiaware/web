<?php

namespace Component\View\Menu;

use Component\View\StatusComponentAbstract;
use Component\ViewModel\Menu\MenuViewModelInterface;
use Component\ViewModel\Menu\Item\ItemViewModel;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;


use Component\Renderer\Html\Menu\MenuWrapRendererInterface;
use Component\View\Menu\LevelComponent;

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

        $editableMode = $this->contextData->presentEditableMenu() AND $this->isAllowedToPresent(AccessPresentationEnum::EDIT);
//        $views = [];
//        foreach ($this->contextData->getItemModels() as $itemViewModel) {
//            /** @var ItemViewModelInterface $itemViewModel */
//            // pokud render používá classMap musí být konfigurován v Renderer kontejneru - tam dostane classMap
//            if($editableMode) {
//                $views[] =  (new ElementComponent($this->configuration))->setData($itemViewModel)->setRendererName($this->itemEditableRendererName)->setRendererContainer($this->rendererContainer);
//            } else {
//                $views[] =  (new ElementComponent($this->configuration))->setData($itemViewModel)->setRendererName($this->itemRendererName)->setRendererContainer($this->rendererContainer);
//            }
//        }
        $this->addSubtreeComponents($this->contextData->getItemModels());
    }

    protected function addSubtreeComponents($subtreeItemModels) {
        $editableMode = $this->contextData->presentEditableMenu() AND $this->isAllowedToPresent(AccessPresentationEnum::EDIT);

        if (!$subtreeItemModels) {
            $wrap = '';
        } else {
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
                    $this->addStackedItems($currDepth, $itemDepth, $itemViewModelStack, $editableMode);
                    $itemViewModelStack[$itemDepth][] = $itemViewModel;
                    $currDepth = $itemDepth;
                } else {
                    $itemViewModelStack[$currDepth][] = $itemViewModel;
                }
            }
            $this->addStackedItems($currDepth, $rootDepth, $itemViewModelStack, $editableMode);
            $wrap = $this->addLastLevel($itemViewModelStack[$rootDepth], $editableMode);
        }
        return $wrap;
    }

    private function addStackedItems($currDepth, $targetDepth, &$itemViewModelStack, $editableMode) {
        for ($i=$currDepth; $i>$targetDepth; $i--) {
            $level = [];
            foreach ($itemViewModelStack[$i] as $stackedItemModel) {
                /** @var ItemViewModelInterface $stackedItemModel */
//                $level[] = $this->itemRenderer->render($stackedItemModel);
                $level[] = $stackedItemModel;
            }
            $wrap = $this->levelWrapRenderer->render(implode(PHP_EOL, $level));
            unset($itemViewModelStack[$i]);
            end($itemViewModelStack[$i-1])->getData()->setInnerHtml($wrap);
        }
    }

    private function addLastLevel($itemViewModelStack, $editableMode) {
        $level = new LevelComponent();
        foreach ($itemViewModelStack as $stackedItemModel) {
            /** @var ItemViewModelInterface $stackedItemModel */
            if($editableMode) {
                $child  (new ElementComponent($this->configuration))->setData($itemViewModel)->setRendererName($this->itemEditableRendererName)->setRendererContainer($this->rendererContainer);
            } else {
                $child =  (new ElementComponent($this->configuration))->setData($itemViewModel)->setRendererName($this->itemRendererName)->setRendererContainer($this->rendererContainer);
            }
            $level->
        }
        $wrap = implode(PHP_EOL, $level);                // nejvyšší úroveň stromu je renderována je do "li", "ul" pak udělá menuWrapRenderer, který je nastaven jako renderer celé komponenty ($this->renderer)
        return $wrap;
    }
}
