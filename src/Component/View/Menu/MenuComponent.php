<?php

namespace Component\View\Menu;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Menu\MenuViewModelInterface;
use Component\ViewModel\Menu\Item\ItemViewModel;

use Pes\View\Renderer\RendererInterface;

use  Component\Renderer\Html\Menu\MenuWrapRendererInterface;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends CompositeComponentAbstract implements MenuComponentInterface {

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
     * @param MenuViewModelInterface $viewModel
     */
    public function __construct(MenuViewModelInterface $viewModel) {
        $this->contextData = $viewModel;
    }

    /**
     *
     * @param $levelWrapRendererName
     * @param $itemRendererName
     * @return \Component\Controller\Authored\MenuComponentInterface
     */
    public function setRenderersNames( $levelWrapRendererName, $itemRendererName): MenuComponentInterface {
        $this->levelWrapRendererName = $levelWrapRendererName;
        $this->itemRendererName = $itemRendererName;
        return $this;
    }

    /**
     *
     * @param string $menuRootName
     * @return \Component\Controller\Authored\MenuComponentInterface
     */
    public function setMenuRootName($menuRootName): MenuComponentInterface {
        $this->componentName = $menuRootName;
        return $this;
    }

    /**
     *
     * @param bool $withTitle
     * @return \Component\Controller\Authored\MenuComponentInterface
     */
    public function withTitleItem($withTitle=false): MenuComponentInterface {
        $this->withTitle = $withTitle;
        return $this;
    }

    /**
     * Renderuje menu a vrací string. Jazyk, uid aktuální položky menu, stav edit použije z presentation status.
     * @return string
     */
    public function beforeRenderingHook(): void {
        // set renderer
        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Komponent ".get_called_class()." nemá nastaven renderer kontejner metodou setRendererContainer().");
        }
        /** @var MenuWrapRendererInterface $renderer */
        $renderer = $this->rendererContainer->get($this->rendererName);
        $renderer->setLevelWrapRenderer($this->rendererContainer->get($this->levelWrapRendererName));
        $renderer->setItemRenderer($this->rendererContainer->get($this->itemRendererName));
        $this->setRenderer($renderer);

        $this->contextData->setMenuRootName($this->componentName);
        $this->contextData->withTitleItem($this->withTitle);
        $this->contextData->setMaxDepth(null);

        return ;
    }

}
