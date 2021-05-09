<?php

namespace Component\ViewModel\Nav;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Component\Renderer\Html\ClassMap\ClassMapInterface;

use Component\ViewModel\StatusViewModelAbstract;
use Component\Renderer\Nav\Factory\UlTagFactory;
use Component\Renderer\Nav\Factory\NavTagFactory;

use Model\Entity\HierarchyAggregateInterface;
use Model\Entity\MenuRootInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;
use Model\Repository\HierarchyAggregateRepo;
use Model\Repository\MenuRootRepo;

use Component\ViewModel\Authored\Menu\Item\ItemViewModel;
use Component\ViewModel\Authored\Menu\Item\ItemViewModelInterface;

/**
 * Description of NavViewModel
 *
 * @author pes2704
 */
class NavViewModel extends StatusViewModelAbstract implements NavViewModelInterface {

    private $menuRootRepo;
    private $HierarchyRepo;
    private $presentedMenuNode;
    private $presentedLangCode;
    private $presentedMenuItem;

    /**
     * @var NavTagFactory
     */
    private $navTagFactory;

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
        $presentationStatus = $this->statusPresentationRepo->get();
        $this->presentedLangCode = $presentationStatus->getLanguage()->getLangCode();
        $this->presentedMenuItem = $presentationStatus->getMenuItem();
    }

    public function setNavTagFactory(NavTagFactory $navTagFactory) {
        $this->navTagFactory = $navTagFactory;
    }

    /**
     *
     * @param string $menuRootName
     * @return MenuRootInterface
     */
    public function getMenuRoot($menuRootName): MenuRootInterface {
        return $this->menuRootRepo->get($menuRootName);
    }

    /**
     * Vrací prezentovanou položku menu. Řídí se stavem PresentationStatus language a menuItem.
     *
     * @return HierarchyAggregateInterface
     */
    public function getPresentedMenuNode(): HierarchyAggregateInterface {
        if(!isset($this->presentedMenuNode)) {
            $this->presentedMenuNode = isset($this->presentedMenuItem) ? $this->getMenuNode($this->presentedMenuItem->getUidFk()) : '';
        }
        return $this->presentedMenuNode ? $this->presentedMenuNode : null;
    }

    /**
     * Vrací položku menu se zadaným uid a v presentovaném jazyce.
     * @param string $nodeUid
     * @return HierarchyAggregateInterface
     */
    public function getMenuNode($nodeUid): HierarchyAggregateInterface {
        return $this->HierarchyRepo->get($this->presentedLangCode, $nodeUid);
    }

    /**
     *
     * @param string $rootUid uid kořenového uzlu požadovaného postromu hierarchie
     * @param int $maxDepth  int or NULL - maximální hloubka vraceného uzlu v rámci celé hierarchie
     * @return ItemViewModel array of
     */
    public function getItemModels($rootUid, $maxDepth=null) {

        $presentedMenuNode = $this->getPresentedMenuNode();
        if (isset($presentedMenuNode)) {
            $presentedUid = $presentedMenuNode->getUid();
            $presentedItemLeftNode = $presentedMenuNode->getLeftNode();
            $presentedItemRightNode = $presentedMenuNode->getRightNode();
        }

        // command
        $pasteUid = $this->getPostFlashCommand('cut');
        $pasteMode = $pasteUid ? true : false;

        $models = [];
        foreach ($this->getNodes($rootUid, $maxDepth) as $node) {
           if (isset($presentedItemLeftNode)) {
                $isOnPath = ($presentedItemLeftNode >= $node->getLeftNode()) && ($presentedItemRightNode <= $node->getRightNode());
            } else {
                $isOnPath = FALSE;
            }
            $nodeUid = $node->getUid();
            $isPresented = isset($presentedUid) ? ($presentedUid == $nodeUid) : FALSE;
            $isCutted = $pasteUid == $nodeUid;

            $readonly = false;
//            $models[] = new ItemViewModel($node, $isOnPath, $isPresented, $isRestored, $readonly);
            $models[] = new ItemViewModel($node, $isOnPath, $isPresented, $pasteMode, $isCutted, $readonly);
            if ($pasteMode) {
                $itemViewModel->setPasteUid($pasteUid);
            }
        }
        return $models;
    }

    private function getNodes($rootUid, $maxDepth=null) {
        $presentationStatus = $this->statusPresentationRepo->get();
        $langCode = $presentationStatus->getLanguage()->getLangCode();
        return $this->HierarchyRepo->getSubTree($langCode, $rootUid, $maxDepth);
    }

    ## nepoužito ##
    public function getFlattenedTree() {
## html
//        $styles = $this->container->get(MenuListStyles::class);
        /* @var $classMap Component\Renderer\Html\ClassMap\ClassMap */
        $classMap = $this->container->get('menu.svisle.classmap');
        /* @var $menuViewModel MenuViewModel */
        $menuViewModel = $this->container->get(MenuViewModel::class);
        // vazba na jQuery:
        $ulElementId = "";
        $ulElementId = $this->container->get('menuUlElementId');

        $subDomain = "/www_grafia_development_v0_5/"; //$this->container->get(ServerRequestInterface::class)->getUri()->getPath();
        /* @var $menuRepo HierarchyAggregateRepo */
        $menuRepo = $this->container->get(HierarchyAggregateRepo::class);

        $mode = 2;
        $langCode = 'cs';
        $active = FALSE;
        $actual = FALSE;

        switch ($mode) {
            // getFullTree
            case 0:
                $flatenedTree = $menuRepo->getFullTree($langCode, $active, $actual);
                break;
            // getSubTree
            case 1:
                // výběr kořenové položky menu -  položku vyhledávám podle Title
                $topMenuItemTitle = "Vzdělávání";
//                $topMenuItemTitle = "Rekvalifikační kurzy";
                $rootNode = $menuRepo->getNodeByTitle($langCode, $topMenuItemTitle, $active, $actual);
//                $flatenedTree = $menuRepo->getSubTree($langCode, $rootNode->getHierarchyUid(), $active, $actual);
                $flatenedTree = $menuViewModel->getSubTreeItemModels($rootNode->getUid());
                break;
            // getSubTree s maximální hloubkou
            case 2:
                // výběr kořenové položky menu -  položku vyhledávám podle Title
                $topMenuItemTitle = "Vzdělávání";
//                $topMenuItemTitle = "Rekvalifikační kurzy";
                $rootNode = $menuRepo->getNodeByTitle($langCode, $topMenuItemTitle, $active, $actual);
                $maxDepth = NULL;
                $maxDepth = 4;   // max depth v celém stromu!!
//                $flatenedTree = $menuRepo->getSubTree($langCode, $rootNode->getHierarchyUid(), $active, $actual, $maxDepth);
                $flatenedTree = $menuViewModel->getSubTreeItemModels($rootNode->getUid(), $maxDepth);
                break;
            // getSubNodes s maximální hloubkou
            case 3:
                // výběr kořenové položky menu -  položku vyhledávám podle Title
                $topMenuItemTitle = "Vzdělávání";
//                $topMenuItemTitle = "Rekvalifikační kurzy";
                $rootNode = $menuRepo->getNodeByTitle($langCode, $topMenuItemTitle, $active, $actual);
                $maxDepth = NULL;
//                $subLevels = 3;   // max depth v celém stromu!!
                $flatenedTree = $menuRepo->getSubNodes($langCode, $rootNode->getUid(), $active, $actual, $maxDepth);
                break;
            // getFullPathWithSiblings
            case 4:
                $parentItemTitle = "Vzdělávání";
                $parentNode = $menuRepo->getNodeByTitle($langCode, $parentItemTitle, $active, $actual);
                $leafItemTitle = "Rekvalifikační kurz ZDARMA";
                $leafNode = $menuRepo->getNodeByTitle($langCode, $leafItemTitle, $active, $actual);
                $flatenedTree = $menuRepo->getFullPathWithSiblings($langCode, $parentNode->getUid(), $leafNode->getUid(), $active, $actual);
                break;
            // singlePath
            case 5:
                $leafItemTitle = "Rekvalifikační kurz ZDARMA";
                $leafNode = $menuRepo->getNodeByTitle($langCode, $leafItemTitle, $active, $actual);
                $flatenedTree = $menuRepo->singlePath($langCode, $leafNode->getUid(), $active, $actual);
                break;
            // getImmediateSubNodes
//            case 6:
//                $parentItemTitle = "Vzdělávání";
//                $parentNode = $readHierarchy->getNodeByTitle($langCode, $parentItemTitle, $active, $actual);
//                $flatenedTree = $readHierarchy->getImmediateSubNodes($langCode, $parentNode['uid'], $active, $actual);
//                break;
            default:
                throw new \UnexpectedValueException("Není nastaven case pro mode $mode");
                break;
        }
        return $flatenedTree;
    }

}
