<?php

namespace Component\View\Menu;

use Component\View\StatusComponentAbstract;
use Component\ViewModel\Menu\MenuViewModelInterface;
use Component\ViewModel\Menu\Item\ItemViewModel;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

use Pes\View\Renderer\RendererInterface;
use Pes\View\CompositeView;

use Component\Renderer\Html\Menu\MenuWrapRendererInterface;
use Component\Renderer\Html\Menu\ItemRenderer;
use Component\Renderer\Html\Menu\ItemRendererEditable;

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

        $this->createItemViews();
    }

    private function createItemViews() {
        $editableMode = $this->contextData->presentEditableMenu() AND $this->isAllowedToPresent(AccessPresentationEnum::EDIT);
        $views = [];
        foreach ($this->contextData->getItemModels() as $depth => $itemViewModel) {
            /** @var ItemViewModelInterface $itemViewModel */
            // pokud render používá classMap musí být konfigurován v Renderer kontejneru - tam dostane classMap
            if($editableMode AND $itemViewModel->isPresented()) {
                $views[] =  (new CompositeView())->setData($itemViewModel)->setRendererName($this->itemEditableRendererName)->setRendererContainer($this->rendererContainer);
            } else {
                $views[] =  (new CompositeView())->setData($itemViewModel)->setRendererName($this->itemRendererName)->setRendererContainer($this->rendererContainer);
            }
        }
        $this->contextData->setSubTreeItemViews($views);
    }

}
