<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Page\Controler;

use Site\ConfigurationCache;

use FrontControler\PresentationFrontControlerAbstract;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Access\AccessPresentationInterface;

use Red\Service\ItemApi\ItemApiServiceInterface;
use Red\Service\CascadeLoader\CascadeLoaderFactoryInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Red\Model\Repository\MenuItemRepo;
use Red\Model\Repository\BlockRepo;

use Red\Model\Entity\MenuItemInterface;

####################

use Red\Service\ItemApi\ItemApiService;
use Red\Service\CascadeLoader\CascadeLoaderFactory;

use Pes\View\View;
use Pes\View\CompositeView;
use Pes\View\CompositeViewInterface;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;

use Access\Enum\RoleEnum;

use Exception;
use UnexpectedValueException;

/**
 * Description of GetControler
 *
 * @author pes2704
 */
abstract class LayoutControlerAbstract extends PresentationFrontControlerAbstract {

    protected $componentViews = [];
    
    /**
     * @var ItemApiServiceInterface
     */
    private $itemApiService;
    
    /**
     * @var CascadeLoaderFactoryInterface
     */
    private $cascadeLoaderFactory;
    
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo, 
            StatusFlashRepo $statusFlashRepo, 
            StatusPresentationRepo $statusPresentationRepo,
            AccessPresentationInterface $accessPresentation,
            ItemApiServiceInterface $itemApiService, 
            CascadeLoaderFactoryInterface $cascadeLoaderFactory ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation);
        $this->itemApiService = $itemApiService;
        $this->cascadeLoaderFactory = $cascadeLoaderFactory;              
    }
    
    /**
     * 
     * @return MenuItemInterface|null
     * @throws UnexpectedValueException
     */
    protected function getHomeMenuItem(): ?MenuItemInterface {
        $homePage = ConfigurationCache::layoutControler()['home_page'];
        switch ($homePage[0]) {
            case 'block':
                try {
                    $menuItem = $this->getMenuItemForBlock($homePage[1]);
                } catch (UnexpectedValueException $exc) {                    
//                    echo $exc->getMessage();
                    $this->statusPresentationRepo->get()->setInfo('home', $exc->getMessage());
                }
                if (!isset($menuItem)) {
//                    throw new UnexpectedValueException("Undefined menu item for default page (home page) defined in configuration as block with name '$homePage[1]'.");
                    $this->statusPresentationRepo->get()->setInfo('home', "Undefined menu item for default page (home page) defined in configuration as block with name '$homePage[1]'.");
                }
                break;
            case 'item':
                $menuItem = $this->getMenuItem($homePage[1]);
                if (!isset($menuItem)) {
//                    throw new UnexpectedValueException("Default page (home page) defined in configuration as item with uid '$homePage[1]' not exists or is not published (active).");
                    $this->statusPresentationRepo->get()->setInfo('home', "Default page (home page) defined in configuration as item with uid '$homePage[1]' not exists or is not published (active).");
                }
                break;
            default:
//                throw new UnexpectedValueException("Unknown home page type in configuration. Type: '$homePage[0]'.");
                    $this->statusPresentationRepo->get()->setInfo('home', "Unknown home page type in configuration. Type: '$homePage[0]'.");
        }
        return $menuItem ?? null;
    }
    
    /**
     * Podle hierarchy uid a aktuálního jazyka prezentace vrací menuItem nebo null
     *
     * @param string $uid
     * @return MenuItemInterface|null
     */
    protected function getMenuItem($uid): ?MenuItemInterface {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $menuItem = $menuItemRepo->get($this->getPresentationLangCode(), $uid);
        return $menuItem ?? null;  // neexistuje nebo není aktivní
    }

    /**
     * Podle jména bloku načte uid odpovídajícího menu item.
     * Pokud blok se zadaným jménem není v databázi definován vyhodí výjimku.
     * Pro uid zjištěného z bloku a aktuálního jazyka prezentace vrací menuItem nebo null, pkud menuitem není definován nebo není publikován (active).
     * 
     * @param type $name
     * @return MenuItemInterface|null
     * @throws UnexpectedValueException
     */
    protected function getMenuItemForBlock($name): ?MenuItemInterface {
        /** @var BlockRepo $blockRepo */
        $blockRepo = $this->container->get(BlockRepo::class);
        $block = $blockRepo->get($name);
        if (!isset($block)) {
            throw new UnexpectedValueException("Block with name '$name' not exists in database.");
        }
        $blockItem = $this->getMenuItem($block->getUidFk());
        return $blockItem ?? null;  // není blok nebo není publikovaný&aktivní item
    }

#
#### response ################################
#
    /**
     * Metoda pro pomtomkovský controler (page controler). Rozšiřuje funkčnost metod FrontControlerAbstract.
     * Vytvoří response pro položku v hierarchii - menu item. Pro neexistující menu item vytvoří response s přesměrováním na "home" stránku.
     *
     * @param ServerRequestInterface $request
     * @param MenuItemInterface $menuItem
     * @return ResponseInterface
     */
    protected function createResponseWithItem(ServerRequestInterface $request, MenuItemInterface $menuItem = null) {
        if ($menuItem) {
            $this->setPresentationMenuItem($menuItem);
        }
            $view = $this->composeLayoutView($request, $menuItem);
            $response = $this->createStringOKResponseFromView($view);
//        } else {
//            // neexistující stránka
//            $response = $this->createResponseRedirectSeeOther($request, ""); // SeeOther - ->home
//        }
        return $response;
    }

#
#### composite view
#
    /**
     * vytvoří kompozitní view z layout template a tomuto view nastaví defaultní data každé stránky.
     * Tomuto view přidá jako komponenty případné zadané komponentní views.
     *
     * @param ServerRequestInterface $request
     * @param MenuItemInterface $menuItem
     * @return type
     */
    private function composeLayoutView(ServerRequestInterface $request, MenuItemInterface $menuItem = null) {
        $layoutView = $this->getLayoutView($request);
        if (isset($menuItem)) {
            $layoutView->appendComponentViews($this->getContentViews($menuItem));
        } else {
            $layoutView->appendComponentView($this->getNoContentView('Error: No item.'), 'content');            
        }
        return $layoutView->appendComponentViews($this->getComponentViews($request));        
    }

    /**
     * View s tempate layout.php a data pro template
     * @return CompositeView
     */
    private function getLayoutView(ServerRequestInterface $request): CompositeViewInterface {
        $navConfigView = $this->container->get(View::class)
                ->setTemplate(new InterpolateTemplate(ConfigurationCache::layoutControler()['templates.navConfig']))
                ->setData([
                    // pro navConfig.js
                    'basePath' => $this->getBasePath($request),  // stejná metoda dáva base path i do layout.php
                    'cascadeClass' => ConfigurationCache::layoutControler()['cascade.class'],
                    'apiActionClass' => ConfigurationCache::layoutControler()['apiaction.class'],
                ]);
        
        /** @var CompositeViewInterface $view */
        $view = $this->container->get(CompositeView::class);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutControler()['templates.layout']));
        $view->setData(
                [
                    // v layout.php
                    'basePath' => $this->getBasePath($request),  // stejná metoda dává base path i do tinyConfig.js
                    'langCode' => $this->getPresentationLangCode(),
                    'title' => ConfigurationCache::layoutControler()['title'],
                    // podmínění insert css souborů v head/css.php
                    'isContentEditable' => $this->isContentEditable(),
                    // na mnoha místech - cesty k souborům zadané v konfiguraci
                    'linksCommon' => ConfigurationCache::layoutControler()['linksCommon'],
                    'linksSite' => ConfigurationCache::layoutControler()['linksSite'],
                    'version' => ConfigurationCache::layoutControler()['version'] ?? '',
                    // js proměnná navConfig - pro volání cascade v body.js
                    'navConfigView' => $navConfigView,
                ]);
        return $view;
    }

    #### komponenty a komponentní views ######

    /**
     *
     * @param ServerRequestInterface $request
     * @param MenuItemInterface $menuItem
     * @return CompositeView[]
     */
    protected function getComponentViews(ServerRequestInterface $request) {
        $views = array_merge(
                $this->isContentEditable() ? $this->getEditableModeViews($request) : [],
                $this->getMenuViews(),
                $this->getBlockLoaders(),
                $this->getCascadeViews(),
                // for debug
//                $this->getEmptyMenuComponents(),
            );
        return $views;
    }

    /**
     * Generuje html obsahující definice tagů <script> vkládaných do stránku pouze v editačním módu
     * @param type $request
     * @return array
     */
    private function getEditableModeViews($request) {
        $tinyLanguage = ConfigurationCache::layoutControler()['tinyLanguage'];
        $langCode =$this->statusPresentationRepo->get()->getLanguageCode();
        $tinyToolsbarsLang = array_key_exists($langCode, $tinyLanguage) ? $tinyLanguage[$langCode] : ConfigurationCache::presentationStatus()['default_lang_code'];
        $tinyConfigView =  $this->container->get(View::class)
                ->setTemplate(new InterpolateTemplate(ConfigurationCache::layoutControler()['templates.tinyConfig']))
                ->setData([
                    // pro tinyConfig.js
                    'basePath' => $this->getBasePath($request),  // stejná metoda dáva base path i do layout.php
                    'toolbarsLang' => $tinyToolsbarsLang,
                    // prvky pole contentCSS - tyto tři proměnné jsou prvky pole - pole je v tiny_config.js v proměnné contentCss
                    'urlStylesCss' => ConfigurationCache::layoutControler()['urlStylesCss'],
                    'urlSemanticCss' => ConfigurationCache::layoutControler()['urlSemanticCss'],
                    'urlContentTemplatesCss' => ConfigurationCache::layoutControler()['urlContentTemplatesCss'],
                    'urlMediaCss' => ConfigurationCache::layoutControler()['urlMediaCss']
                ]);        
        $views = [];                    
        $redScriptsView = $this->container->get(View::class)
                ->setTemplate(new PhpTemplate(ConfigurationCache::layoutControler()['templates.redScripts']))
                ->setData([
                    'tinyConfigView' => $tinyConfigView,
                    'urlTinyMCE' => ConfigurationCache::layoutControler()['urlTinyMCE'],
    //                    'urlJqueryTinyMCE' => ConfigurationCache::layoutControler()['urlJqueryTinyMCE'],
                    'urlTinyInit' => ConfigurationCache::layoutControler()['urlTinyInit'],
                    'urlEditScript' => ConfigurationCache::layoutControler()['urlEditScript'],
                    ]);
        $views ['redScripts'] = $redScriptsView;
//        $views += $this->getLayoutEditableViews();
        $views += $this->getMenuEditableViews();        
        return $views;
    }
    
    private function getLayoutViews() {
        $views = [];
        foreach (array_keys(ConfigurationCache::layoutControler()['contextLayoutMap']) as $contextName) {
            $views[$contextName] = $this->cascadeLoaderFactory->getRedLoaderElement("red/v1/service/$contextName", ConfigurationCache::layoutControler()['cascade.cacheLoadOnce']);             
        }
        return $views;
    }
    
    private function getLayoutEditableViews() {
        $views = [];
        foreach (array_keys(ConfigurationCache::layoutControler()['contextLayoutEditableMap']) as $contextName) {
            $views[$contextName] = $this->cascadeLoaderFactory->getRedLoaderElement("red/v1/service/$contextName", ConfigurationCache::layoutControler()['cascade.cacheLoadOnce']);             
        }
        return $views;
    }
    
    private function getContentViews(MenuItemInterface $menuItem) {
        //TODO:  !! provizorní řešení pro pouze jednu "target" proměnnou v kontextu (jedno místo pro content)
        $dataRedApiUri = $this->itemApiService->getContentApiUri($menuItem);
        $views = [];
        foreach (ConfigurationCache::layoutControler()['contextTargetMap'] as $contextName=>$targetSettings) {
            // 'content'=>['id'=>'menutarget_content'],
            $id = $targetSettings['id'];
            $views[$contextName] = $this->cascadeLoaderFactory->getRedTargetElement($id, $dataRedApiUri, ConfigurationCache::layoutControler()['cascade.cacheLoadOnce']);            
        }
        return $views;
    }
    
    private function getCascadeViews() {
        $views = [];
        foreach (ConfigurationCache::layoutControler()['contextServiceMap'] as $contextName=>$route) {
            $views[$contextName] = $this->cascadeLoaderFactory->getRedLoaderElement(array_key_first($route), ConfigurationCache::layoutControler()['cascade.cacheReloadOnNav']);             
        }
        return $views;
    }
    
    private function getMenuViews() {
        $views = [];
        foreach (ConfigurationCache::layoutControler()['contextMenuMap'] as $contextName=>$menuSettings) {
            //'menuSvisle' => ['service'=>'menuVertical', 'targetContext'=>'content'],
            //'contextTargetMap' => [
            //        'content'=>['id'=>'menutarget_content'],  
            //    ]
            $targetId = ConfigurationCache::layoutControler()['contextTargetMap'][$menuSettings['targetContext']]['id'];
            $views[$contextName] = $this->cascadeLoaderFactory->getRedLoaderElement("red/v1/service/$contextName", ConfigurationCache::layoutControler()['cascade.cacheLoadOnce'], $targetId);
        }
        return $views;
    }
    
    private function getMenuEditableViews() {
        $views = [];
        foreach (ConfigurationCache::layoutControler()['contextMenuEditableMap'] as $contextName=>$menuSettings) {
            // 'menuSvisle' => ['service'=>'menuVertical', 'targetId'=>'menutarget_content'],
            $targetId = $menuSettings['targetId'];
            $views[$contextName] = $this->cascadeLoaderFactory->getRedLoaderElement("red/v1/service/$contextName", ConfigurationCache::layoutControler()['cascade.cacheLoadOnce'], $targetId);
        }
        return $views;
    }    
#
#### view s content loaderem #####################################################
#
#
#### menu item loadery pro bloky layoutu #########################################################################
#

    /**
     * Vrací pole komponentů pro zobrazení obsahů v místech bloků layoutu. Pro bloky definované v konfiguraci vytvoří pole komponentních view obsahujících
     * skript pro načtení obsahu pomocí cascade.js v dalším requestu generovaném v prohlížeči.
     *
     * @return View[]
     */
    private function getBlockLoaders() {
        $map = ConfigurationCache::layoutControler()['contextBlocksMap'];
        $componets = [];

        // pro neexistující bloky nedělá nic
        foreach ($map as $variableName => $blockName) {
            try {
                $menuItem = $this->getMenuItemForBlock($blockName);                
            } catch (UnexpectedValueException $exc) {  // neexistuje block
                $componets[$variableName] = $this->getUnknownBlockView($blockName, $variableName);
            }
            if (isset($menuItem)) {
                $componets[$variableName] = $this->getMenuItemLoader($menuItem);
            } else {
                $componets[$variableName] = $this->getUnknownBlockView($blockName, $variableName);  // neex nebo neaktivní item
            }
        }
        return $componets;
    }
    
    /**
     * Vrací view s šablonou obsahující skript pro načtení obsahu na základě typu menuItem a id menu item. Načtení probíhá pomocí cascade.js.
     * cascade.js odešle request, získaným obsahem a zamění původní obsah html elementu v layoutu.
     * Parametry uri v načítacím skriptu jsou typ menuItem a id menu item, aby nebylo třeba načítat data s obsahem (paper, article, multipage a další) zde v kontroleru.
     * Pro případ obsahu typu 'static' jsou jako prametry uri předány typ 'static' a jméno statické stránky, které je pak použito pro načtení statické šablony.
     *
     * @param type $menuItem
     * @return View
     */
    private function getMenuItemLoader(MenuItemInterface $menuItem) {
        $dataRedApiUri = $this->itemApiService->getContentApiUri($menuItem);
        if($this->isContentEditable()) {
            return $this->cascadeLoaderFactory->getRedLoaderElement($dataRedApiUri, ConfigurationCache::layoutControler()['cascade.cacheLoadOnce']); 
        } else {
            return $this->cascadeLoaderFactory->getRedLoaderElement($dataRedApiUri, ConfigurationCache::layoutControler()['cascade.cacheReloadOnNav']);             
        }
    }
    
    private function getNoContentView($message) {
        /** @var View $view */
        $view = $this->container->get(View::class);
        $view->setTemplate(new PhpTemplate(ConfigurationCache::layoutControler()['templates.unknownContent']))
                ->setData(['message'=>$message]);
        return $view;
    }
    
    private function getUnknownBlockView($blockName, $variableName) {
        $message = "Unknown block $blockName configured for layout variable $variableName.";
        return $this->getNoContentView($message);
    }

    private function isContentEditable() {
        //TODO: SV !!NUTNÉ!! zde se rozhoduje, jestli se načítají skripty potřebné pro editaci 
        $statusSecurity = $this->statusSecurityRepo->get();
        $userActions = $statusSecurity->getEditorActions();
        $representativeActions = $statusSecurity->getRepresentativeActions();
        $loginAgg = $statusSecurity->getLoginAggregate();
        $role = isset($loginAgg) ? $loginAgg->getCredentials()->getRoleFk() : '';
        return 
        // editor
        (isset($userActions) && $userActions->presentEditableContent()) 
        // representative
        || (isset($representativeActions) && $representativeActions->getDataEditable())
        // visitor - nemá přepínač edituj data - edituje pořád
        || ($role==RoleEnum::VISITOR)
//        // events administrator
//        || (isset($role) && $role==RoleEnum::EVENTS_ADMINISTRATOR)
        ;
    }

}
