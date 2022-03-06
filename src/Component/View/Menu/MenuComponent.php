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

    const TOGGLE_EDIT_MENU = 'toggleEditMenuButton';

    /**
     * @var MenuViewModelInterface
     */
    protected $contextData;

    protected $levelWrapRendererName;
    protected $itemRendererName;

    /**
     * @var RendererInterface
     */
    protected $levelWrapRenderer;

    /**
     * @var RendererInterface
     */
    protected $itemRenderer;

    protected $active = TRUE;
    protected $actual = TRUE;
    protected $langCode;
    protected $rootUid;
    protected $withTitle = false;

    protected $componentName;
    protected $presentedUid;
    protected $presentedItemLeftNode;
    protected $presentedItemRightNode;
    protected $presentRenderer;

    /**
     *
     * @param $levelWrapRendererName
     * @return MenuComponentInterface
     */
    public function setRenderersNames( $levelWrapRendererName): MenuComponentInterface {
        $this->levelWrapRendererName = $levelWrapRendererName;
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
        /** @var MenuWrapRendererInterface $renderer */
        $renderer = $this->rendererContainer->get($this->rendererName);
        $renderer->setLevelWrapRenderer($this->rendererContainer->get($this->levelWrapRendererName));
        $this->setRenderer($renderer);

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
                $views[] =  (new CompositeView())->setData($itemViewModel)->setRendererName('menu.itemrenderer.editable')->setRendererContainer($this->rendererContainer);
            } else {
                $views[] =  (new CompositeView())->setData($itemViewModel)->setRendererName('menu.itemrenderer')->setRendererContainer($this->rendererContainer);
            }
        }
        $this->contextData->setSubTreeItemViews($views);
    }

}
