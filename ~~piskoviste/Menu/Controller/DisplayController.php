<?php

namespace Menu\Middleware\Menu\Controller;

use Menu\Middleware\Controller\FrontControlerAbstract;

use Menu\Helper\TestHelper;

use Menu\Middleware\Menu\MenuContainerConfigurator;
use Pes\Container\Container;

use Menu\Psr\Http\Message\ServerRequestInterface;

use Menu\Model\HierarchyHooks\MenuListStyles;
use Menu\Model\Repository\HierarchyAggregateRepo;

// propojení s middleware web
use Menu\Component\ViewModel\Menu\MenuViewModel;
use Menu\Component\ViewModel\Menu\ItemViewModel;



use Menu\Middleware\Menu\View\Factory\NavTagFactory;
use Menu\Middleware\Menu\View\Factory\UlTagFactory;

use Menu\Middleware\Menu\View\PageView;
use Menu\Middleware\Menu\View\DetailView;
// varianta:
use Menu\Middleware\Menu\View\MenuView;

use Menu\Middleware\Menu\View\Renderer\PageRenderer;
use Menu\Middleware\Menu\View\Renderer\DetailRenderer;

use Pes\View\View;
use Pes\View\Template\NodeTemplate;
use Pes\Http\Response;

/**
 * Description of Controller
 *
 * @author pes2704
 */
class DisplayController extends FrontControlerAbstract {

    public function content($id=NULL) {

## html
//        $styles = $this->container->get(MenuListStyles::class);
        /* @var $classMap Web\Component\Renderer\Html\ClassMap\ClassMap */
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
                $flatenedTree = $menuViewModel->getSubTreeNodes($rootNode->getUid());
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
                $flatenedTree = $menuViewModel->getSubTreeNodes($rootNode->getUid(), $maxDepth);
                break;
            // getSubNodes s maximální hloubkou
            case 3:
                // výběr kořenové položky menu -  položku vyhledávám podle Title
                $topMenuItemTitle = "Vzdělávání";
//                $topMenuItemTitle = "Rekvalifikační kurzy";
                $rootNode = $menuRepo->getNodeByTitle($langCode, $topMenuItemTitle, $active, $actual);
                $maxDepth = NULL;
//                $subLevels = 3;   // max depth v celém stromu!!
                $flatenedTree = $menuRepo->getSubTree($langCode, $rootNode->getUid(), $active, $actual, $maxDepth);
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

        $ulTagFactory = new UlTagFactory($flatenedTree, $classMap, $ulElementId, $subDomain);
        $nav = (new NavTagFactory($ulTagFactory, $ulElementId))->createTag();
        $navHtml = (new View())->setTemplate(new NodeTemplate($nav))->getString();                             // 80 ms
        $detailHtml = (new View())->setRenderer(new DetailRenderer())->setData(['id' => $id])->getString();
        $pegeView = (new View())->setRenderer(new PageRenderer())->setData(['nav'=>$navHtml, 'detail'=>$detailHtml, 'subDomain' => $subDomain]);

## send html
        $response = new Response();
        $response->getBody()->write($pegeView->getString());
        return $response;
    }
}