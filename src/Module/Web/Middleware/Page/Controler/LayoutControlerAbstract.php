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
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Repository\BlockRepo;
use Red\Model\Entity\BlockInterface;

use Red\Model\Repository\StaticItemRepoInterface;
use Red\Model\Repository\StaticItemRepo;
use Red\Model\Entity\StaticItemInterface;

####################

use Red\Service\ItemApi\ItemApiService;
use Red\Service\CascadeLoader\CascadeLoaderFactory;

use Pes\View\View;
use Pes\View\CompositeView;
use Pes\View\CompositeViewInterface;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;

use Access\Enum\RoleEnum;

use UnexpectedValueException;
use Web\Middleware\Page\Controler\Exception\NoItemException;
use Web\Middleware\Page\Controler\Exception\NoBlockException;

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
    
    protected function getSafeItem($uid): MenuItemInterface {
        try {
            $langCode = $this->getPresentationLangCode();
            $menuItem = $this->getMenuItem($langCode, $uid);
        } catch (NoItemException $exc) {
            //TODO: log
            $languageCode = ConfigurationCache::presentationStatus()['default_lang_code'];
            $this->setPresentationLangCode($languageCode);
            $this->addFlashMessage('Default language set.');            
        try {
            //TODO: log
            $menuItem = $this->getMenuItem($languageCode, $uid);
            
        } catch (NoItemException $exc) {
            $this->addFlashMessage('Redirect: item to home.');   
            $menuItem = $this->getSafeHomeItem();
        }
            
        }
        return $menuItem;
    }

    protected function getSafeHomeItem(): MenuItemInterface {
        // home block na základě konfigurace
        $homeBlockName = $this->getHomePageBlockName();  // exc neošetřená - fatal - chyba v konfiguraci 
        $homeBlock = $this->getBlock($homeBlockName);  // exc neošetřená - fatal - chyba v databázi
        try {
            // skutečný home item
            $languageCode = $this->getPresentationLangCode();            
            $homeItem = $this->getMenuItem($languageCode, $homeBlock->getUidFk()); // pokud existuje záznam v block - musí existovat i item s uid - integrita pomocí cizího klíče v db
            // nalezen skutečný home item
        } catch (NoItemException $exc) {
            // není item pro home block uid
            // -> není presentation lang code - chybně fungující ukládání informací do session - prohlížeče s ochanou proti cookies, reklamám apod.
            //TODO: log
            $languageCode = ConfigurationCache::presentationStatus()['default_lang_code'];
            $this->addFlashMessage('Default language set.');
            $this->setPresentationLangCode($languageCode);            
            $homeItem = $this->getMenuItem($languageCode, $homeBlock->getUidFk());    // exc neošetřená - fatal - stránka není zveřejněná    
        }    
        return $homeItem;
    }
    
    protected function responseForItem(ServerRequestInterface $request, MenuItemInterface $menuItem): ResponseInterface {
        // set status
        $this->setPresentationMenuItem($menuItem);
        /** @var StaticItemRepoInterface $staticItemRepo */
        $staticItemRepo = $this->container->get(StaticItemRepo::class); 
        $staticItem = $staticItemRepo->getByMenuItemId($menuItem->getId());
        $this->setPresentationStaticItem($staticItem);  // static item nebo null
        // create view
        $view = $this->composeLayoutView($request, $menuItem);
        // create response
        return $this->createStringOKResponseFromView($view);
    }
    
    ## private
    
    /**
     * Podle hierarchy uid a aktuálního jazyka prezentace vrací entitu MenuItem.
     *
     * @param string $uid
     * @return MenuItemInterface
     * @throws NoItemException
     */
    private function getMenuItem($langCode, $uid): MenuItemInterface {
        /** @var MenuItemRepo $menuItemRepo */
        $menuItemRepo = $this->container->get(MenuItemRepo::class);
        $menuItem = $menuItemRepo->get($langCode, $uid);
        if (!isset($menuItem)) {
            throw new NoItemException("No published item in database for langCode '$langCode' and uid '$uid'.");
        }
        return $menuItem;
    }
    
    private function getFallbackBlock(): BlockInterface {
        $fallbackName = ConfigurationCache::layoutControler()['homePageFallbackBlockName'];
        try {
            $fallbackBlock = $this->getBlock($fallbackName); 
        } catch (NoBlockException $exc) {
            throw new NoBlockException("The fallback block named '$fallbackName' does not exist in the database. Check configuration.");            
        }
        return $fallbackBlock;
    }

    /**
     * Podle jména bloku načte z databáze uid odpovídajícího menu item.
     * Pokud blok se zadaným jménem není v databázi definován vyhodí výjimku NoBlockException.
     * 
     * @param string $name
     * @return string
     * @throws NoBlockException
     */
    protected function getBlock($name): BlockInterface {
        /** @var BlockRepo $blockRepo */
        $blockRepo = $this->container->get(BlockRepo::class);
        $block = $blockRepo->get($name);
        if (!isset($block)) {
            throw new NoBlockException("The block named '$name' does not exist in the database.");
        }
        return $block;
    }
    
    /**
     * Vrací jméno bloku pro home page z konfigurace.
     * Pokud jméno bloku pro home page v konfiguraci není, vyhodí výjimku.
     * 
     * @return type
     * @throws UnexpectedValueException
     */
    protected function getHomePageBlockName() {
        $homePageName = ConfigurationCache::layoutControler()['homePageBlockName'];
        if (!$homePageName??'') {
                throw new UnexpectedValueException("Undefined name of the home page (default page) in the configuration.");
        }
        return $homePageName;
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
    protected function composeLayoutView(ServerRequestInterface $request, MenuItemInterface $menuItem = null) {
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

    #### conzent #####
        
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
                $this->isContentEditable() ? $this->getMenuEditableViews() : [],
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
                $menuItem = $this->getBlock($blockName);                
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
