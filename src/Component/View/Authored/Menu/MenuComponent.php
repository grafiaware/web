<?php

namespace Component\View\Authored\Menu;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\Menu\MenuViewModelInterface;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

use Pes\View\Renderer\RendererInterface;

use  Component\Renderer\Html\Authored\Menu\MenuWrapRendererInterface;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends CompositeComponentAbstract implements MenuComponentInterface {

    /**
     * @var MenuViewModelInterface
     */
    protected $viewModel;

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
        $this->viewModel = $viewModel;
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
    public function getString() {
        // set renderer
        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Komponent ".get_called_class()." nemá nastaven renderer kontejner metodou setRendererContainer().");
        }
        /** @var MenuWrapRendererInterface $renderer */
        $renderer = $this->rendererContainer->get($this->rendererName);
        
        //MenuWrapRenderer je nastaven jako renderer (rodičovského) view => je-li typu RendererModelAwareInterface dostane $this->viewModel voláním metody ->setViewModel($this->viewModel)
        //v Pes View
        //ItemRenderer dostane $this->viewModel v metodě MenuWrapRendererAbstract->getMenuHtml(), t.j. při renderování menuItemModel
        //LevelWrapRenderer musí dostat $this->viewModel tady
        
        //TODO: celé je to pěkný ... dávám viewModel do rendereru - asi kvůli Paper nebo Article rendereru s template - ?? je to nutné?
        $levelWrapRenderer = $this->rendererContainer->get($this->levelWrapRendererName);
        $levelWrapRenderer->setViewModel($this->viewModel);
        $renderer->setLevelWrapRenderer($levelWrapRenderer);
        $renderer->setItemRenderer($this->rendererContainer->get($this->itemRendererName));
        $this->setRenderer($renderer);

        $this->viewModel->setMenuRootName($this->componentName);
        $this->viewModel->withTitleItem($this->withTitle);
        $this->viewModel->setMaxDepth(null);

        return parent::getString();
    }

}
