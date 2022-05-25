<?php
namespace Component\ViewModel\Menu;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModel;

use Red\Model\Entity\MenuItemInterface;

use Red\Model\Entity\HierarchyAggregate;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuRootInterface;

use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuRootRepo;

use Component\ViewModel\Menu\Item\ItemViewModel;
use Component\ViewModel\Menu\Item\ItemViewModelInterface;

/**
 * Description of MenuViewModel
 *
 * @author pes2704
 */
class MenuViewModel extends ViewModelAbstract implements MenuViewModelInterface {

    private $status;
    private $menuRootRepo;
    private $hierarchyRepo;
    private $presentedMenuNode;
    //TODO: stavové proměnné menu - kvůli nim musí být MenuViewModel vyráběn v kontejneru pomocí factory
    private $menuRootName;
    private $maxDepth;
    private $withRootItem;

    private $models = [];
    private $itemViews = [];

    public function __construct(
            StatusViewModel $status,
            HierarchyJoinMenuItemRepo $hierarchyRepo,
            MenuRootRepo $menuRootRepo
            ) {
        $this->status = $status;
        $this->hierarchyRepo = $hierarchyRepo;
        $this->menuRootRepo = $menuRootRepo;
    }

    public function presentEditableMenu(): bool {
        return $this->status->presentEditableMenu();
    }

    /**
     *
     * @return bool
     */
    public function presentOnlyPublished(): bool {
        return ! $this->status->presentEditableContent();  //negace
    }

    /**
     * Nastaví jméno kořene menu
     *
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
     * {@inheritDoc}
     *
     * @param type $withRootItem
     * @return void
     */
    public function withRootItem($withRootItem = false): void {
        $this->withRootItem = $withRootItem;
    }

    /**
     * {@inheritdoc}
     *
     * @return HierarchyAggregateInterface|null
     */
    public function getPresentedMenuNode(HierarchyAggregateInterface $rootNode): ?HierarchyAggregateInterface {
        if(!isset($this->presentedMenuNode)) {
            $presentedMenuItem = $this->status->getPresentedMenuItem();
            if (isset($presentedMenuItem)) {
                $presented = $this->getMenuNode($presentedMenuItem);
                if ($presented->getLeftNode() >= $rootNode->getLeftNode() AND $presented->getLeftNode() < $rootNode->getRightNode()) {
                    $this->presentedMenuNode = $presented;
                }
            }
        }
        return $this->presentedMenuNode;
    }

    /**
     * Vrací položku hierarchie (menu node) pro zadaný menu item a prezentovaný jazyk.
     *
     * @param MenuItemInterface $menuItem
     * @return HierarchyAggregateInterface|null
     */
    private function getMenuNode(MenuItemInterface $menuItem): ?HierarchyAggregateInterface {
        return $this->hierarchyRepo->get($this->presentedLanguageLangCode(), $menuItem->getUidFk());
    }

    private function presentedLanguageLangCode() {
        return $this->status->getPresentedLanguage()->getLangCode();
    }
    /**
     * Původní metoda getSubtreeItemModel pro Menu Display Controller. Načte podstrom uzlů menu, potomkků
     *
     * @return ItemViewModelInterface array af
     */
    public function getSubTreeNodes() {
        // root uid z jména komponenty
        if (!isset($this->menuRootName)) {
            user_error("Název kořene menu nebyl zadán. Název kořenr menu je nutné zadat metodou setMenuRootName().", E_USER_WARNING);
        }
        $menuRoot = $this->menuRootRepo->get($this->menuRootName);
        if (!isset($menuRoot)) {
            user_error("Kořen menu se zadaným jménem '$this->menuRootName' nebyl načten z tabulky kořenů menu.", E_USER_WARNING);
        }
        // nodes
        $nodes = $this->hierarchyRepo->getSubTree($this->presentedLanguageLangCode(), $menuRoot->getUidFk(), $this->maxDepth);

        return $nodes ?? [];
    }

    /**
     * Cache
     * Asociativní pole depth=>ItemViewModel pro generování ul, li struktury v rendereru.
     *
     * @return array
     */
    public function getItemModels(): array {
        if (! $this->models) {   //prázdné pole
            $this->models = $this->prepareItemModels();
        }
        return $this->models;
    }

    /**
     * Pole ItemViewModel pro generování ul, li struktury v rendereru
     * @return array
     */
    private function prepareItemModels() {
//            $nodes, $presentedNode=null) {
        $nodes = $this->getSubTreeNodes();
        $rootNode = reset($nodes);
            // remove root
//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
        if (!$this->withRootItem) {
            $removed = array_shift($nodes);   //odstraní první prvek s indexem [0] a výsledné pole opět začína prvkem s indexem [0]
        }
        $presentedNode = $this->getPresentedMenuNode($rootNode);
        if (isset($presentedNode)) {
            $presentedUid = $presentedNode->getUid();
            $presentedItemLeftNode = $presentedNode->getLeftNode();
            $presentedItemRightNode = $presentedNode->getRightNode();
        }

        // command
        $pasteUid = $this->status->getFlashPostCommand('cut');
        $pasteMode = $pasteUid ? true : false;

        //editable menu
        $menuEditable = $this->status->presentEditableMenu();

        // minimální hloubka u menu bez zobrazení kořenového prvku je 2 (pro 1 je nodes pole v modelu prázdné), u menu se zobrazením kořenového prvku je minimálmí hloubka 1, ale nodes pak obsahuje jen kořenový prvek
        if (empty($nodes)) {
            $rootDepth = 1;
        } else {
//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
            $rootDepth = reset($nodes)->getDepth();  //jako side efekt resetuje pointer
        }
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
        if (!$isLeaf) {
            $title = $node->getMenuItem()->getTitle();
        }
            $itemViewModel = new ItemViewModel($node, $realDepth, $isOnPath, $isLeaf, $isPresented, $pasteMode, $isCutted, $menuEditable);

            $models[] = $itemViewModel;
        }
        return $models;
    }

    public function setSubTreeItemViews($itemViews) {
        $this->itemViews = $itemViews;
    }

    public function getSubTreeItemViews() {
        return $this->itemViews;
    }
}
