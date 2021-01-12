<?php

namespace Component\View\Authored\Menu;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\Menu\MenuViewModel;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

use Pes\View\Renderer\RendererInterface;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends CompositeComponentAbstract implements MenuComponentInterface {

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
        $menuRoot = $this->viewModel->getMenuRoot($this->componentName);
        if (!isset($menuRoot)) {
            user_error("Kořen menu se zadaným jménem komponety '$this->componentName' nebyl načten z tabulky kořenů menu.", E_USER_WARNING);
        }
        $this->rootUid = $menuRoot->getUidFk();


        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Komponent ".get_called_class()." nemá nastaven renderer kontejner metodou setRendererContainer().");
        }
        $this->setRenderer($this->rendererContainer->get($this->rendererName));
        $this->levelWrapRenderer = $this->rendererContainer->get($this->levelWrapRendererName);
        $this->itemRenderer = $this->rendererContainer->get($this->itemRendererName);

        $subtreeItemModels = $this->viewModel->getSubTreeItemModels($this->rootUid, null);

        if ($this->withTitle) {
            $rootItemModel = $subtreeItemModels[0];
            if (isset($rootItemModel)) {
                $titleItemHtml = $this->itemRenderer->render($rootItemModel);
            } else {
                $titleItemHtml = '';  // root menu item nená publikovaný
            }
        } else {
            $titleItemHtml = '';
        }
        return parent::getString($data ? $data : $titleItemHtml . $this->getMenuLevelHtml($subtreeItemModels));
    }

    protected function getMenuLevelHtml($subtreeItemModels) {
        $itemTags = [];

//        $subtreeItemModels = $this->viewModel->getSubTreeItemModels($parentUid, null);
        $first = true;
        foreach ($subtreeItemModels as $itemModel) {
            /** @var ItemViewModel $itemModel */
            $itemDepth = $itemModel->getMenuNode()->getDepth();
            if ($first) {
                $rootDepth = $itemDepth;
                $currDepth = $itemDepth;
                $first = false;
            }
            if ($itemDepth>$currDepth) {
                $currDepth = $itemDepth;
                $itemStack[$currDepth][] = $itemModel;
            } elseif ($itemDepth<$currDepth) {
                for ($i=$currDepth; $i>$itemDepth; $i--) {
                    $level = [];
                    foreach ($itemStack[$i] as $stackedItemModel) {
                        $level[] = $this->itemRenderer->render($stackedItemModel);
                    }
                    $wrap = $this->levelWrapRenderer->render(implode(PHP_EOL, $level));
                    unset($itemStack[$i]);
                    end($itemStack[$i-1])->setInnerHtml($wrap);
                }
                $currDepth = $itemDepth;
                $itemStack[$currDepth][] = $itemModel;
            } else {
                $itemStack[$currDepth][] = $itemModel;
            }
        }
        for ($i=$currDepth; $i>$rootDepth; $i--) {
            $level = [];
            foreach ($itemStack[$i] as $stackedItemModel) {
                $level[] = $this->itemRenderer->render($stackedItemModel);
            }
            if ($i-$rootDepth==1) {
                $wrap = implode(PHP_EOL, $level);                // nejvyšší úroveň stromu je renderována je do "li", "ul" pak udělá menuWrapRenderer, který je nastaven jako renderer celé komponenty ($this->renderer)
            } else {
                $wrap = $this->levelWrapRenderer->render(implode(PHP_EOL, $level[$i]));
            }
            unset($itemStack[$i]);
            end($itemStack[$i-1])->setInnerHtml($wrap);
        }
        return end($itemStack[$rootDepth])->getInnerHtml();
    }

    // to do menu level rendereru ($this->presented... si bude brát z view modelu
    protected function getMenuLevelHtml_OLD($parentUid) {
        $itemTags = [];

//        $subtreeItemModels = $this->viewModel->getChildrenItemModels($parentUid);
        $subtreeItemModels = $this->viewModel->getSubTreeItemModels($parentUid, null);
        foreach ($subtreeItemModels as $itemViewModel) {    // , $maxDepth
            if($itemViewModel->isOnPath()) {
                $innerHtml = $this->levelWrapRenderer->render($this->getMenuLevelHtml_OLD($itemViewModel->getMenuNode()->getUid()));
                $itemViewModel->setInnerHtml($innerHtml);
            }
            $itemTags[] = $this->itemRenderer->render($itemViewModel);

        }
        return $itemTags ? \implode(PHP_EOL, $itemTags) : '';
    }

}
