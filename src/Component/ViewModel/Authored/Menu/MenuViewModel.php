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
    private $HierarchyRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            HierarchyAggregateRepo $hierarchyRepo,
            MenuRootRepo $menuRootRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->HierarchyRepo = $hierarchyRepo;
        $this->menuRootRepo = $menuRootRepo;
    }

    /**
     * Vrací prezentovanou položku menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return HierarchyAggregateInterface
     */
    public function getPresentedMenuNode() {
        $presentationStatus = $this->statusPresentationRepo->get();
        $presentedMenuItem = $presentationStatus->getMenuItem();
        return isset($presentedMenuItem) ? $this->HierarchyRepo->get($presentationStatus->getLanguage()->getLangCode(), $presentationStatus->getMenuItem()->getUidFk()) : null;
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
        return $this->HierarchyRepo->get($presentationStatus->getLanguage()->getLangCode(), $nodeUid);
    }

    /**
     *
     * @param string $parentUid
     * @return HierarchyAggregateInterface array af
     */
    public function getChildrenMenuNodes($parentUid) {
        $presentationStatus = $this->statusPresentationRepo->get();
        return $this->HierarchyRepo->findChildren($presentationStatus->getLanguage()->getLangCode(), $parentUid);
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
        $pastedUid = $this->getPostFlashCommand('cut');
        if ($pastedUid) {
            $modeCommand = ['paste' => $pastedUid];
        }

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
            //TODO: ($menuNode, $isOnPath, $isPresented, $isRestored, $readonly, $innerHtml='')
            $isCutted = $pastedUid == $nodeUid;
//            $readonly = $node->getUid()==$this->rootUid;
            $itemViewModel = new ItemViewModel($node, $isOnPath, $isPresented, $isCutted, false);
            if (isset($modeCommand)) {
                $itemViewModel->setModeCommand($modeCommand);
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

        $presentationStatus = $this->getStatusPresentation();
        $items = $this->HierarchyRepo->getSubTree($presentationStatus->getLanguage()->getLangCode(), $rootUid, $maxDepth);
        $models = [];
        foreach ($items as $item) {
           if (isset($presentedItemLeftNode)) {
                $isOnPath = ($presentedItemLeftNode >= $item->getLeftNode()) && ($presentedItemRightNode <= $item->getRightNode());
            } else {
                $isOnPath = FALSE;
            }
            $isPresented = isset($presentedUid) ? ($presentedUid == $item->getHierarchyUid()) : FALSE;
            //TODO: ($menuNode, $isOnPath, $isPresented, $isRestored, $readonly, $innerHtml='')
            $isRestored = false;
            $readonly = false;
            $models[] = new ItemViewModel($item, $isOnPath, $isPresented, $isRestored, $readonly);
        }
        return $models;
    }
}
