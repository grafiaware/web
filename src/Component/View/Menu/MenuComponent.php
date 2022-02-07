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

use Component\View\AllowedActionEnum;

/**
 * Description of MenuComponent
 *
 * @author pes2704
 */
class MenuComponent extends StatusComponentAbstract implements MenuComponentInterface {

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
        $renderer->setItemRenderer($this->rendererContainer->get($this->itemRendererName));
        $this->setRenderer($renderer);

        $this->contextData->setMenuRootName($this->componentName);
        $this->contextData->setMaxDepth(null);

        $models = $this->prepareItemModels($this->contextData);
        $views = $this->createItemViews($models);
        $this->contextData->setSubtreeItemViews($views);
        return ;
    }

    private function prepareItemModels(MenuViewModelInterface $viewModel) {
//            $nodes, $presentedNode=null) {
        $nodes = $viewModel->getSubTreeNodes();
        $rootNode = reset($nodes);
            // remove root
//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
        if (!$this->withTitle) {
            $removed = array_shift($nodes);   //odstraní první prvek s indexem [0] a výsledné pole opět začína prvkem s indexem [0]
        }
        $presentedNode = $viewModel->getPresentedMenuNode($rootNode);
        if (isset($presentedNode)) {
            $presentedUid = $presentedNode->getUid();
            $presentedItemLeftNode = $presentedNode->getLeftNode();
            $presentedItemRightNode = $presentedNode->getRightNode();
        }

        // command
        $pasteUid = $viewModel->getPostFlashCommand('cut');
        $pasteMode = $pasteUid ? true : false;

        //editable menu
        $menuEditable = $viewModel->presentEditableMenu();

//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
        $rootDepth = reset($nodes)->getDepth();  //jako side efekt resetuje pointer
        $models = [];
        foreach ($nodes as $key => $node) {
            /** @var HierarchyAggregateInterface $node */
            $realDepth = $node->getDepth() - $rootDepth + 1;  // první úroveň má realDepth=1
            $isOnPath = isset($presentedNode) ? ($presentedItemLeftNode >= $node->getLeftNode()) && ($presentedItemRightNode <= $node->getRightNode()) : FALSE;
            $isLeaf = (
                        (($node->getRightNode() - $node->getLeftNode()) == 1)   //žádný potomek
                        OR
                        (!array_key_exists($key+1, $nodes))  // žádný aktivní (zobrazený) potomek - je poslední v poli $nodes
                        OR
                        ($nodes[$key+1]->getDepth() <= $node->getDepth())  // žádný aktivní (zobrazený) potomek - další prvek $nodes nemá větší hloubku
                    );
            $nodeUid = $node->getUid();
            $isPresented = isset($presentedUid) ? ($presentedUid == $nodeUid) : FALSE;
            $isCutted = $pasteUid == $nodeUid;

            $itemViewModel = new ItemViewModel($node, $realDepth, $isOnPath, $isLeaf, $isPresented, $pasteMode, $isCutted, $menuEditable);

            $models[$realDepth] = $itemViewModel;
        }
        return $models;
    }

    private function createItemViews($itemViewModels) {
        foreach ($itemViewModels as $depth => $itemVieModel) {
            /** @var ItemViewModelInterface $itemVieModel */
            // pokud render používá classMap musí být konfigurován v Renderer kontejneru - tam dostane classMap
            if($this->contextData->presentEditableMenu() AND $this->isAllowed($this, AllowedActionEnum::EDIT)) {
                $view =  (new CompositeView())->setData($itemVieModel)->setRendererName(ItemRendererEditable::class)->setRendererContainer($this->rendererContainer);
            } else {
                $view =  (new CompositeView())->setData($itemVieModel)->setRendererName(ItemRenderer::class)->setRendererContainer($this->rendererContainer);
            }
            // $itemVieModel !! není iterable!
            $views[$depth] = $view;
        }
        return $views ?? [];
    }
}
