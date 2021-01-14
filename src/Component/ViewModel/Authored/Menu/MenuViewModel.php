<?php
namespace Component\ViewModel\Authored\Menu;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

use Model\Entity\HierarchyAggregateInterface;
use Model\Entity\MenuRootInterface;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\HierarchyAggregateRepo;
use Model\Repository\MenuRootRepo;

use Component\ViewModel\Authored\Menu\Item\ItemViewModel;
use Component\ViewModel\Authored\Menu\Item\ItemViewModelInterface;

/**
 * Description of MenuViewModel
 *
 * @author pes2704
 */
class MenuViewModel extends AuthoredViewModelAbstract implements MenuViewModelInterface {

    private $menuRootRepo;
    private $hierarchyRepo;
    private $presentedMenuNode;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            HierarchyAggregateRepo $hierarchyRepo,
            MenuRootRepo $menuRootRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->hierarchyRepo = $hierarchyRepo;
        $this->menuRootRepo = $menuRootRepo;
    }

    /**
     * Vrací prezentovanou položku menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return HierarchyAggregateInterface
     */
    public function getPresentedMenuNode() {
        if(!isset($this->presentedMenuNode)) {
            $presentationStatus = $this->statusPresentationRepo->get();
            $presentedMenuItem = $presentationStatus->getMenuItem();
            $this->presentedMenuNode = isset($presentedMenuItem) ? $this->getMenuNode($presentedMenuItem->getUidFk()) : '';
        }
        return $this->presentedMenuNode ? $this->presentedMenuNode : null;
    }

    /**
     *
     * @param string $menuRootName
     * @return MenuRootInterface
     */
    public function getMenuRoot($menuRootName) {
        return $this->menuRootRepo->get($menuRootName);
    }

    /**
     * Vrací položku menu se zadaným uid a v presentovaném jazyce.
     * @param string $nodeUid
     * @return HierarchyAggregateInterface
     */
    public function getMenuNode($nodeUid) {
        $presentationStatus = $this->statusPresentationRepo->get();
        return $this->hierarchyRepo->get($presentationStatus->getLanguage()->getLangCode(), $nodeUid);
    }

    /**
     *
     * @param string $parentUid
     * @return HierarchyAggregateInterface array af
     */
    public function getChildrenMenuNodes($parentUid) {
        $presentationStatus = $this->statusPresentationRepo->get();
        return $this->hierarchyRepo->findChildren($presentationStatus->getLanguage()->getLangCode(), $parentUid);
    }

    /**
     *
     * @param type $parentUid
     * @param type $maxDepth
     * @return ItemViewModelInterface array of
     */
    public function getChildrenItemModels($parentUid) {

        $presentedItem = $this->getPresentedMenuNode();
        if (isset($presentedItem)) {
            $presentedUid = $presentedItem->getUid();
            $presentedItemLeftNode = $presentedItem->getLeftNode();
            $presentedItemRightNode = $presentedItem->getRightNode();
        }

        // command
        $pasteUid = $this->getPostFlashCommand('cut');
        $pasteMode = $pasteUid ? true : false;

        $nodes = $this->getChildrenMenuNodes($parentUid);
        $models = [];
        foreach ($nodes as $node) {
            if (isset($presentedItemLeftNode)) {
                $isOnPath = ($presentedItemLeftNode >= $node->getLeftNode()) && ($presentedItemRightNode <= $node->getRightNode());
            } else {
                $isOnPath = FALSE;
            }
            $nodeUid = $node->getUid();
            $isPresented = isset($presentedUid) ? ($presentedUid == $nodeUid) : FALSE;
            $isCutted = $pasteUid == $nodeUid;
//            $readonly = $node->getUid()==$this->rootUid;
            $itemViewModel = new ItemViewModel($node, $isOnPath, $isPresented, $pasteMode, $isCutted, false);
            if ($pasteMode) {
                $itemViewModel->setPasteUid($pasteUid);
            }
            $models[] = $itemViewModel;
        }
        return $models;
    }

    /**
     * Původní metoda getSubtreeItemModel pro Menu Display controler
     *
     * @param type $rootUid
     * @param type $maxDepth
     * @return ItemViewModelInterface array af
     */
    public function getSubTreeItemModels($rootUid, $maxDepth=NULL) {

        $presentedItem = $this->getPresentedMenuNode();
        if (isset($presentedItem)) {
            $presentedUid = $presentedItem->getUid();
            $presentedItemLeftNode = $presentedItem->getLeftNode();
            $presentedItemRightNode = $presentedItem->getRightNode();
        }

        // command
        $pasteUid = $this->getPostFlashCommand('cut');
        $pasteMode = $pasteUid ? true : false;

        $presentationStatus = $this->statusPresentationRepo->get();
        $langCode = $presentationStatus->getLanguage()->getLangCode();
        $nodes = $this->hierarchyRepo->getSubTree($langCode, $rootUid, $maxDepth);
        $rootDepth = $nodes[0]->getDepth();
        $models = [];
        foreach ($nodes as $node) {
           if (isset($presentedItemLeftNode)) {
                $isOnPath = ($presentedItemLeftNode >= $node->getLeftNode()) && ($presentedItemRightNode <= $node->getRightNode());
            } else {
                $isOnPath = FALSE;
            }
            $realDepth = $node->getDepth() - $rootDepth;
            $nodeUid = $node->getUid();
            $isPresented = isset($presentedUid) ? ($presentedUid == $nodeUid) : FALSE;
            $isCutted = $pasteUid == $nodeUid;
            $models[] = new ItemViewModel($node, $realDepth, $isOnPath, $isPresented, $pasteMode, $isCutted, false);
            if ($pasteMode) {
                $itemViewModel->setPasteUid($pasteUid);
            }
        }
        return $models;
    }
}
