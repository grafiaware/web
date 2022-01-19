<?php
namespace Component\ViewModel\Menu;

use Component\ViewModel\StatusViewModel;

use Red\Model\Entity\HierarchyAggregate;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuRootInterface;

use Status\Model\Repository\{StatusSecurityRepo, StatusPresentationRepo, StatusFlashRepo};

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
    private $withRoot = false;
    private $menuRootName;
    private $maxDepth;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            HierarchyJoinMenuItemRepo $hierarchyRepo,
            MenuRootRepo $menuRootRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
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
     * @param bool $withTitle
     * @return void
     */
    public function withTitleItem($withTitle=false): void {
        $this->withRoot = $withTitle;
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
    public function getSubTreeItemModels() {
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

        // presented node
        $rootNode = reset($nodes);
        $presentedNode = $this->getPresentedMenuNode($rootNode);

        // remove root
//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
        if (!$this->withRoot) {
            $removed = array_shift($nodes);   //odstraní první prvek s indexem [0] a výsledné pole opět začína prvkem s indexem [0]
        }
        return $nodes ? $this->createItemModels($nodes, $presentedNode) : [];
    }

    private function createItemModels($nodes, $presentedNode=null) {

        if (isset($presentedNode)) {
            $presentedUid = $presentedNode->getUid();
            $presentedItemLeftNode = $presentedNode->getLeftNode();
            $presentedItemRightNode = $presentedNode->getRightNode();
        }

        // command
        $pasteUid = $this->getPostFlashCommand('cut');
        $pasteMode = $pasteUid ? true : false;

        //editable menu
        $menuEditable = $this->presentEditableMenu();

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
            if ($isPresented) {
                $isMenuEditableByUser = $this->isMenuEditableByUser();  // volá se jen pro presented = jednou
            } else {
                $isMenuEditableByUser = false;
            }

            $itemViewModel = new ItemViewModel($node, $realDepth, $isOnPath, $isLeaf, $isPresented, $isMenuEditableByUser, $pasteMode, $isCutted, $menuEditable);

            $models[] = $itemViewModel;
        }
        return $models;
    }


    /**
     * Editovat smí uživatel s rolí 'sup'
     *
     * @return bool
     */
    public function isMenuEditableByUser(): bool {
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        if ($loginAggregate) {
            $isSupervisor = $loginAggregate->getCredentials()->getRole() == 'sup';
            return ($this->presentEditableMenu() AND $isSupervisor);
        } else {
            return false;
        }
    }

    public function getIterator(): \Traversable {
        return new \ArrayObject(
                [
                    'subTreeItemModels' => $this->getSubTreeItemModels(),
                ]
                );
    }
}
