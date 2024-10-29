<?php
namespace Red\Component\ViewModel\Menu;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModel;

use Red\Model\Entity\MenuItemInterface;

use Red\Model\Entity\HierarchyAggregate;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuRootInterface;

use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Repository\MenuRootRepo;

use Red\Component\ViewModel\Menu\ItemViewModelInterface;
use Red\Component\ViewModel\Menu\DriverViewModel;
use Red\Component\ViewModel\Menu\DriverViewModelInterface;

use Red\Service\ItemApi\ItemApiServiceInterface;

use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;

/**
 * Description of MenuViewModel
 *
 * @author pes2704
 */
class MenuViewModel extends ViewModelAbstract implements MenuViewModelInterface {

    /**
     * @var StatusViewModel
     */
    private $statusViewModel;

    private $menuRootRepo;
    private $hierarchyRepo;
    
    private $presentedMenuNode;
    //TODO: stavové proměnné menu - kvůli nim musí být MenuViewModel vyráběn v kontejneru pomocí factory
    private $menuRootName;
    private $maxDepth;
    private $withRootItem;
    private $itemType;

    private $models = [];
    private $itemViews = [];

    public function __construct(
            StatusViewModel $status,
            HierarchyJoinMenuItemRepo $hierarchyRepo,
            MenuRootRepo $menuRootRepo
            ) {
        $this->statusViewModel = $status;
        $this->hierarchyRepo = $hierarchyRepo;
        $this->menuRootRepo = $menuRootRepo;
    }

    public function presentEditableContent(): bool {
        return $this->statusViewModel->presentEditableContent();
    }

    /**
     *
     * @return bool
     */
    public function presentOnlyPublished(): bool {
        return ! $this->statusViewModel->presentEditableContent();  //negace
    }

    /**
     * Nastaví jméno kořene menu. Musí jít o jméno uvedené v db tabulce menu_root. Podle tohoto jména
     * se určí kořenová položka menu.
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

    public function getPostCommand($key) {
        return $this->statusViewModel->getFlashPostCommand($key);
    }

    /**
     * {@inheritdoc}
     *
     * @return HierarchyAggregateInterface|null
     */
    public function getPresentedMenuNode(HierarchyAggregateInterface $rootNode): ?HierarchyAggregateInterface {
        if(!isset($this->presentedMenuNode)) {
            $presentedMenuItem = $this->getPresentedMenuItem();
            if (isset($presentedMenuItem)) {
                $presented = $this->getMenuNode($presentedMenuItem);
                if (isset($presented) AND $presented->getLeftNode() >= $rootNode->getLeftNode() AND $presented->getLeftNode() < $rootNode->getRightNode()) {
                    $this->presentedMenuNode = $presented;
                }
            }
        }
        //TODO: po ukončení editace nepublikované stránky je $this->presentedMenuNode i $presentedMenuItem null -> nezobrazí se žádný obsah (pokud nedošlo v ctrl k přesměrování
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

    public function presentedLanguageLangCode() {
        return $this->statusViewModel->getPresentedLanguage()->getLangCode();
    }
    
    public function getPresentedMenuItem() {
        return $this->statusViewModel->getPresentedMenuItem();
    }
    /**
     * Původní metoda getSubtreeItemModel pro Menu Display Controller. Načte podstrom uzlů menu, potomkků
     *
     * @return array
     */
    public function getSubTreeNodes() {
        // root uid z jména komponenty
        if (!isset($this->menuRootName)) {
            user_error("Název kořene menu nebyl zadán. Název kořene menu je nutné zadat metodou setMenuRootName().", E_USER_WARNING);
        }
        $menuRoot = $this->menuRootRepo->get($this->menuRootName);
        if (!isset($menuRoot)) {
            user_error("Kořen menu se zadaným jménem '$this->menuRootName' nebyl načten z tabulky kořenů menu.", E_USER_WARNING);
        }
        // nodes
        $nodes = $this->hierarchyRepo->findSubTree($this->presentedLanguageLangCode(), $menuRoot->getUidFk(), $this->maxDepth);

        return $nodes ?? [];
    }

    /**
     * Cache
     * Asociativní pole depth=>ItemViewModel pro generování ul, li struktury v rendereru.
     *
     * @return array
     */
    public function getNodeModels(): array {
        $nodes = $this->getSubTreeNodes();
        $rootNode = reset($nodes); 

        $presentedNode = $this->getPresentedMenuNode($rootNode);
        if (isset($presentedNode)) {
            $presentedItemLeftNode = $presentedNode->getLeftNode();
            $presentedItemRightNode = $presentedNode->getRightNode();
        }
        // remove root v needitačním režimu (v needitačním režimu se nikdy nezobrazuje menu root, v editačním režimu se vždy zobrazuje menu root)
//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
        if (!$this->presentEditableContent()) {
            array_shift($nodes);   //odstraní první prvek s indexem [0] a výsledné pole opět začína prvkem s indexem [0]
        }

        // minimální hloubka u menu bez zobrazení kořenového prvku je 2 (pro 1 je nodes pole v modelu prázdné),
        // u menu se zobrazením kořenového prvku je minimálmí hloubka 1, ale nodes pak obsahuje jen kořenový prvek
        if (empty($nodes)) {
            $rootDepth = 1;
        } else {
//        since PHP 7.3 the first value of $array may be accessed with $array[array_key_first($array)];
            $rootDepth = reset($nodes)->getDepth();  //jako side efekt resetuje pointer
        }
        
        $models = [];
        foreach ($nodes as $key => $node) {
            /** @var HierarchyAggregateInterface $node */
            $menuItem = $node->getMenuItem();
            
            $realDepth = $node->getDepth() - $rootDepth + 1;  // první úroveň má realDepth=1
            $isOnPath = isset($presentedNode) ? ($presentedItemLeftNode >= $node->getLeftNode()) && ($presentedItemRightNode <= $node->getRightNode()) : FALSE;
            $isLeaf = (
                        (($node->getRightNode() - $node->getLeftNode()) == 1)   //žádný potomek
                        OR
                        (!array_key_exists($key+1, $nodes))  // žádný aktivní (zobrazený) potomek - je poslední v poli $nodes
                        OR
                        ($nodes[$key+1]->getDepth() <= $node->getDepth())  // žádný aktivní (zobrazený) potomek - další prvek $nodes nemá větší hloubku
                    );

            $models[] = 
            [
                'realDepth'=>$realDepth, 'isOnPath'=>$isOnPath, 'isLeaf'=>$isLeaf,
                'menuItem'=>$menuItem
            ];
        }
        return $models;
    }
}