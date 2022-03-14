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
                $views[] =  (new CompositeView())->setData($itemViewModel)->setRendererName($this->itemEditableRendererName)->setRendererContainer($this->rendererContainer);
            } else {
                $views[] =  (new CompositeView())->setData($itemViewModel)->setRendererName($this->itemRendererName)->setRendererContainer($this->rendererContainer);
            }
        }
        $this->contextData->setSubTreeItemViews($views);
    }

    private function getMenuRendererMap($type) {
        $map = [
            'items' => ['noneditable' => 'menu.itemrenderer', 'editable' => 'menu.itemrenderer.editable'],
            'blocks' => ['noneditable' => 'menu.itemblockrenderer', 'editable' => 'menu.itemblockrenderer.editable'],
            'trash' => ['noneditable' => 'menu.itemtrashrenderer', 'editable' => 'menu.itemtrashrenderer.editable'],
        ];
        switch ($type) {
            case '':


                break;

            default:
                break;
        }
        return $map;
    }

}
