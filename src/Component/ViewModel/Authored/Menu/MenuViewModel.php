<?php
namespace Component\ViewModel\Authored\Menu;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

use Model\Entity\MenuNodeInterface;
use Model\Entity\MenuRootInterface;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\MenuRepo;
use Model\Repository\MenuRootRepo;

use Component\ViewModel\Authored\Menu\Item\ItemViewModel;

/**
 * Description of MenuViewModel
 *
 * @author pes2704
 */
class MenuViewModel extends AuthoredViewModelAbstract implements MenuViewModelInterface {

    private $menuRootRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            MenuRepo $menuRepo,
            MenuRootRepo $menuRootRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $menuRepo);
        $this->menuRootRepo = $menuRootRepo;
    }

    /**
     * Vrací prezentovanou položku menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuNodeInterface
     */
    public function getPresentedMenuNode() {
        $presentationStatus = $this->statusPresentationRepo->get();
        $this->menuRepo->setOnlyPublishedMode($this->presentOnlyPublished(!$this->userEdit())); //!
        return $this->menuRepo->get($presentationStatus->getLanguage()->getLangCode(), $presentationStatus->getMenuItem()->getUidFk());
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
     *
     * @param string $nodeUid
     * @return MenuNodeInterface
     */
    public function getMenuNode($nodeUid) {
        $presentationStatus = $this->statusPresentationRepo->get();
        return $this->menuRepo->get($presentationStatus->getLanguage()->getLangCode(), $nodeUid);
    }

    /**
     *
     * @param string $parentUid
     * @return MenuNodeInterface array af
     */
    public function getChildrenMenuNodes($parentUid) {
        $presentationStatus = $this->statusPresentationRepo->get();
        return $this->menuRepo->findChildren($presentationStatus->getLanguage()->getLangCode(), $parentUid);
    }

    /**
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
        $items = $this->menuRepo->getSubTree($presentationStatus->getLanguage()->getLangCode(), $rootUid, $maxDepth);
        $models = [];
        foreach ($items as $item) {
           if (isset($presentedItemLeftNode)) {
                $isOnPath = ($presentedItemLeftNode >= $item->getLeftNode()) && ($presentedItemRightNode <= $item->getRightNode());
            } else {
                $isOnPath = FALSE;
            }
            $isPresented = isset($presentedUid) ? ($presentedUid == $item->getHierarchyUid()) : FALSE;
            $models[] = new ItemViewModel($item, $isOnPath, $isPresented, $readonly);
        }
        return $models;
    }
}
