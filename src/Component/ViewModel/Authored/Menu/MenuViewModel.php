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
    private $withRoot = false;
    private $menuRootBlockName;
    private $maxDepth;

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
     * Nstaví jméni bloku, jehož ko
     * @param string $blockName
     * @return void
     */
    public function setMenuRootName($blockName): void {
        $this->menuRootBlockName = $blockName;
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
    public function getMenuRoot($menuRootName) {
        return $this->menuRootRepo->get($menuRootName);
    }

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
     * Původní metoda getSubtreeItemModel pro Menu Display controler. Načte podstrom uzlů menu, potomkků
     *
     * @return ItemViewModelInterface array af
     */
    public function getSubTreeItemModels() {
        // root uid z jména komponenty
        $menuRoot = $this->getMenuRoot($this->menuRootBlockName);
        if (!isset($menuRoot)) {
            user_error("Kořen menu se zadaným jménem komponety '$this->menuRootBlockName' nebyl načten z tabulky kořenů menu.", E_USER_WARNING);
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

//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
        $rootDepth = reset($nodes)->getDepth();  //jako side efekt resetuje pointer
        $models = [];
        foreach ($nodes as $key => $node) {
            $realDepth = $node->getDepth() - $rootDepth + 1;  // první úroveň má realDepth=1
            if (isset($presentedNode)) {
                $isOnPath = ($presentedItemLeftNode >= $node->getLeftNode()) && ($presentedItemRightNode <= $node->getRightNode());
            } else {
                $isOnPath = FALSE;
            }
            $isLeaf = (
                        (($node->getRightNode() - $node->getLeftNode()) == 1)   //žádný potomek
                        OR
                        (!array_key_exists($key+1, $nodes))  // žádný aktivní (zobrazený) potomek - je poslední v poli $nodes
                        OR
                        ($nodes[$key+1]->getDepth() <= $node->getDepth())  // žádný aktivní (zobrazený) potomek - další prvek $nodes nemá větší hloubku
                    );
//            $isLeaf1 = $isLeaf2 = $isLeaf3 = false;
//            $isLeaf1 = ($node->getRightNode() - $node->getLeftNode()) == 1;   //žádný potomek
//            if(!$isLeaf1) {
//                $isLeaf2 = !array_key_exists($key+1, $nodes);  // žádný aktivní (zobrazený) potomek - je poslední v poli $nodes
//            }
//            if(!$isLeaf1 AND !$isLeaf2) {
//                $isLeaf3 = $nodes[$key+1]->getDepth() <= $node->getDepth();  // žádný aktivní (zobrazený) potomek - další prvek $nodes nemá větší hloubku
//            }
//            $isLeaf = ($isLeaf1 OR $isLeaf2 OR $isLeaf3);
            $nodeUid = $node->getUid();
            $isPresented = isset($presentedUid) ? ($presentedUid == $nodeUid) : FALSE;
            $isCutted = $pasteUid == $nodeUid;
            $readonly = false;

            $itemViewModel = new ItemViewModel($node, $realDepth, $isOnPath, $isLeaf, $isPresented, $pasteMode, $isCutted, $readonly);
            if ($pasteMode) {
                $itemViewModel->setPasteUid($pasteUid);
            }
            $models[] = $itemViewModel;
        }
        return $models;
    }

    public function getIterator(): \Traversable {
        return new \ArrayObject(
                [
                    'subTreeItemModels' => $this->getSubTreeItemModels(),
                ]
                );
    }
}
