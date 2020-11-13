<?php

namespace Component\View\Authored\Menu;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Menu\MenuViewModel;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends AuthoredComponentAbstract implements MenuComponentInterface {

    protected $viewModel;

    protected $levelWrapRendererName;
    protected $itemRendererName;
    protected $levelWrapRenderer;
    protected $itemRenderer;

    protected $active = TRUE;
    protected $actual = TRUE;
    protected $langCode;
    protected $rootUid;
    protected $withTitle;

    protected $componentName;
    protected $presentedUid;
    protected $presentedItemLeftNode;
    protected $presentedItemRightNode;
    protected $presentRenderer;

    /**
     *
     * @param MenuViewModel $viewModel
     */
    public function __construct(MenuViewModel $viewModel) {
        $this->viewModel = $viewModel;
    }

    /**
     *
     * @param $levelWrapRendererName
     * @param $itemRendererName
     * @return \Component\Controler\Authored\MenuComponentInterface
     */
    public function setRenderersNames( $levelWrapRendererName, $itemRendererName): MenuComponentInterface {
        $this->levelWrapRendererName = $levelWrapRendererName;
        $this->itemRendererName = $itemRendererName;
        return $this;
    }

    /**
     *
     * @param string $componentName
     * @return \Component\Controler\Authored\MenuComponentInterface
     */
    public function setMenuRootName($componentName): MenuComponentInterface {
        $this->componentName = $componentName;
        return $this;
    }

    /**
     *
     * @param bool $withTitle
     * @return \Component\Controler\Authored\MenuComponentInterface
     */
    public function withTitleItem($withTitle=false): MenuComponentInterface {
        $this->withTitle = $withTitle;
        return $this;
    }

    /**
     * Renderuje menu a vrací string. Jazyk, uid aktuální položky menu, stav edit použije z presentation status.
     * @return string
     */
    public function getString($data=null) {

        // toto do view modelu
        $presentedItem = $this->viewModel->getPresentedMenuNode();
        if (isset($presentedItem)) {
            $this->presentedUid = $presentedItem->getUid();
            $this->presentedItemLeftNode = $presentedItem->getLeftNode();
            $this->presentedItemRightNode = $presentedItem->getRightNode();
        }
        $rootItem = $this->viewModel->getMenuRoot($this->componentName);
        if (!isset($rootItem)) {
            user_error("Kořen menu se zadaným jménem komponety '$this->componentName' nebyl načten z tabulky kořenů menu.", E_USER_WARNING);
        }
        $this->rootUid = $rootItem->getUidFk();


        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Komponent ".get_called_class()." nemá nastaven renderer kontejner metodou setRendererContainer().");
        }
        $this->setRenderer($this->rendererContainer->get($this->rendererName));
        $this->levelWrapRenderer = $this->rendererContainer->get($this->levelWrapRendererName);
        $this->itemRenderer = $this->rendererContainer->get($this->itemRendererName);
        if ($this->withTitle) {
            $rootMenuNode = $this->viewModel->getMenuNode($this->rootUid);
            if (isset($rootMenuNode)) {
                $titleItemHtml = $this->itemRenderer->render(
            // (HierarchyAggregateInterface $menuNode, $isOnPath, $isPresented, $isCutted, $readonly)
                    new ItemViewModel($this->viewModel->getMenuNode($this->rootUid), TRUE, $this->presentedUid==$this->rootUid, false, true)
                    );
            } else {
                $titleItemHtml = '';  // root menu item nená publikovaný
            }
        } else {
            $titleItemHtml = '';
        }
        return parent::getString($data ? $data : $titleItemHtml . $this->getMenuLevelHtml($this->rootUid));
    }

    // to do menu level rendereru ($this->presented... si bude brát z view modelu
    protected function getMenuLevelHtml($parentUid) {
        $itemTags = [];

        $subtreeItemModels = $this->viewModel->getChildrenItemModels($parentUid);
        foreach ($subtreeItemModels as $itemViewModel) {    // , $maxDepth
            if($itemViewModel->isOnPath()) {
                $innerHtml = $this->levelWrapRenderer->render($this->getMenuLevelHtml($itemViewModel->getMenuNode()->getUid()));
                $itemViewModel->setInnerHtml($innerHtml);
            }
            $itemTags[] = $this->itemRenderer->render($itemViewModel);

        }
        return $itemTags ? \implode(PHP_EOL, $itemTags) : '';
    }
}
