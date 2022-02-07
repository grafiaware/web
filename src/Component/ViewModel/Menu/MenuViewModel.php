<?php
namespace Component\ViewModel\Menu;

use Component\ViewModel\StatusViewModel;

use Red\Model\Entity\HierarchyAggregate;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuRootInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuRootRepo;

use Component\ViewModel\Menu\Item\ItemViewModel;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

/**
 * Description of MenuViewModel
 *
 * @author pes2704
 */
class MenuViewModel extends StatusViewModel implements MenuViewModelInterface {

    private $menuRootRepo;
    private $hierarchyRepo;
    private $presentedMenuNode;
    private $menuRootName;
    private $maxDepth;

    private $models;


    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            ItemActionRepo $itemActionRepo,
            HierarchyJoinMenuItemRepo $hierarchyRepo,
            MenuRootRepo $menuRootRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $itemActionRepo);
        $this->hierarchyRepo = $hierarchyRepo;
        $this->menuRootRepo = $menuRootRepo;
    }

    /**
     *
     * @return bool
     */
    public function presentOnlyPublished() {
        return ! $this->presentEditableContent();  //negace
    }

    /**
     * Nstaví jméni bloku, jehož ko
     * @param string $blockName
     * @return void
     */
    public function setMenuRootName($blockName): void {
        $this->menuRootName = $blockName;
    }

    /**
     *
     * @param int $maxDepth
     * @return void
     */
    public function setMaxDepth($maxDepth): void {
        $this->maxDepth = $maxDepth;
    }

    /**
     * {@inheritdoc}
     *
     * @return HierarchyAggregateInterface|null
     */
    public function getPresentedMenuNode(HierarchyAggregateInterface $rootNode): ?HierarchyAggregateInterface { // jen mezi left a rifht - uprav
        if(!isset($this->presentedMenuNode)) {
            $presentationStatus = $this->statusPresentationRepo->get();
            $presentedMenuItem = $presentationStatus->getMenuItem();
            if ($presentedMenuItem) {
                $presented = $this->getMenuNode($presentedMenuItem->getUidFk());
                if ($presented->getLeftNode() >= $rootNode->getLeftNode() AND $presented->getLeftNode() < $rootNode->getRightNode()) {
                    $this->presentedMenuNode = $presented;
                }
            }
        }
        return $this->presentedMenuNode;
    }

    /**
     *
     * @param string $menuRootName
     * @return MenuRootInterface
     */
//    public function getMenuRoot($menuRootName) {
//        return $this->menuRootRepo->get($menuRootName);
//    }

    /**
     * Vrací položku menu se zadaným uid a v presentovaném jazyce.
     *
     * @param string $nodeUid
     * @return HierarchyAggregateInterface
     */
    public function getMenuNode($nodeUid): ?HierarchyAggregateInterface {
        $presentationStatus = $this->statusPresentationRepo->get();
        return $this->hierarchyRepo->get($presentationStatus->getLanguage()->getLangCode(), $nodeUid);
    }

    /**
     * Původní metoda getSubtreeItemModel pro Menu Display Controller. Načte podstrom uzlů menu, potomkků
     *
     * @return ItemViewModelInterface array af
     */
    public function getSubTreeNodes() {
        // root uid z jména komponenty
        $menuRoot = $this->menuRootRepo->get($this->menuRootName);
        if (!isset($menuRoot)) {
            user_error("Kořen menu se zadaným jménem komponety '$this->menuRootName' nebyl načten z tabulky kořenů menu.", E_USER_WARNING);
        }
        $rootUid = $menuRoot->getUidFk();
        // nodes
        $presentationStatus = $this->statusPresentationRepo->get();
        $langCode = $presentationStatus->getLanguage()->getLangCode();
        $nodes = $this->hierarchyRepo->getSubTree($langCode, $rootUid, $this->maxDepth);

        return $nodes ?? [];
    }

    public function setSubtreeItemViews($models) {
        $this->models = $models;
    }

    public function getSubTreeItemViews() {
        return $this->models;
    }


    public function getIterator(): \Traversable {
        return new \ArrayObject(
                [
                    'subTreeItemModels' => $this->getSubTreeNodes(),
                ]
                );
    }
}
