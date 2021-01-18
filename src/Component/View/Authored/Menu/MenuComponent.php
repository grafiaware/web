<?php

namespace Component\View\Authored\Menu;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\Menu\MenuViewModel;
use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

use Pes\View\Renderer\RendererInterface;
use Component\Renderer\Html\Menu\MenuWrapRendererInterface;

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
    protected $withTitle = false;

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
//    public function getString($data=null) {
    public function getString($data=null) {
        // set renderer
        if (!isset($this->rendererContainer)) {
            throw new \LogicException("Komponent ".get_called_class()." nemá nastaven renderer kontejner metodou setRendererContainer().");
        }
        /** @var MenuWrapRendererInterface $renderer */
        $renderer = $this->rendererContainer->get($this->rendererName);
        $renderer->setLevelWrapRenderer($this->rendererContainer->get($this->levelWrapRendererName));
        $renderer->setItemRenderer($this->rendererContainer->get($this->itemRendererName));
        $this->setRenderer($renderer);

        $this->viewModel->setMenuRootName($this->componentName);
        $this->viewModel->withTitleItem($this->withTitle);
        $this->viewModel->setMaxDepth(null);


//        $subtreeItemModels = $this->viewModel->getSubTreeItemModels($this->componentName, $this->withTitle, null);

        return parent::getString();
    }

//    private function getMenuLevelHtml($subtreeItemModels) {
//        $itemTags = [];
//        $first = true;
//        foreach ($subtreeItemModels as $itemModel) {
//            /** @var ItemViewModel $itemModel */
//            $itemDepth = $itemModel->getRealDepth();
//            if ($first) {
//                $rootDepth = $itemDepth;
//                $currDepth = $itemDepth;
//                $first = false;
//            }
//            if ($itemDepth>$currDepth) {
//                $itemStack[$itemDepth][] = $itemModel;
//                $currDepth = $itemDepth;
//            } elseif ($itemDepth<$currDepth) {
//                $this->renderStackedItems($currDepth, $itemDepth, $itemStack);
//                $itemStack[$itemDepth][] = $itemModel;
//                $currDepth = $itemDepth;
//            } else {
//                $itemStack[$currDepth][] = $itemModel;
//            }
//        }
//        $this->renderStackedItems($currDepth, $rootDepth, $itemStack);
//        $wrap = $this->renderLastLevel($itemStack[$rootDepth]);
//        return $wrap;
//    }
//
//    private function renderStackedItems($currDepth, $targetDepth, &$itemStack) {
//        for ($i=$currDepth; $i>$targetDepth; $i--) {
//            $level = [];
//            foreach ($itemStack[$i] as $stackedItemModel) {
//                /** @var ItemViewModel $stackedItemModel */
//                $level[] = $this->itemRenderer->render($stackedItemModel);
//            }
//            $wrap = $this->levelWrapRenderer->render(implode(PHP_EOL, $level));
//            unset($itemStack[$i]);
//            end($itemStack[$i-1])->setInnerHtml($wrap);
//        }
//    }
//
//    private function renderLastLevel($itemStack) {
//        $level = [];
//        foreach ($itemStack as $stackedItemModel) {
//            /** @var ItemViewModel $stackedItemModel */
//            $level[] = $this->itemRenderer->render($stackedItemModel);
//        }
//        $wrap = implode(PHP_EOL, $level);                // nejvyšší úroveň stromu je renderována je do "li", "ul" pak udělá menuWrapRenderer, který je nastaven jako renderer celé komponenty ($this->renderer)
//        return $wrap;
//    }

}
