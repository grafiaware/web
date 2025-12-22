<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Middleware\Page\Controler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Access\AccessPresentationInterface;
use Red\Service\ItemApi\ItemApiServiceInterface;
use Red\Service\CascadeLoader\CascadeLoaderFactoryInterface;
use Template\Compiler\TemplateCompilerInterface;

use Site\ConfigurationCache;

// komponenty
use Red\Component\View\Generated\SearchResultComponent;

use Red\Model\Entity\MenuItemInterface;

use UnexpectedValueException;
use Web\Middleware\Page\Controler\Exception\NoItemException;
use Web\Middleware\Page\Controler\Exception\NoBlockException;

/**
 * Description of GetControler
 *
 * @author pes2704
 */
class PageControler extends LayoutControlerAbstract implements PageControlerInterface {

    const HEADER = 'X-WEB-PageCtrl-Time';

    private $templateCompiler;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo, 
            StatusFlashRepo $statusFlashRepo, 
            StatusPresentationRepo $statusPresentationRepo, 
            AccessPresentationInterface $accessPresentation, 
            ItemApiServiceInterface $itemApiService, 
            CascadeLoaderFactoryInterface $cascadeLoaderFactory,
            TemplateCompilerInterface $templateCompiler
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation, $itemApiService, $cascadeLoaderFactory);
        $this->templateCompiler = $templateCompiler;
    }
    
    ### action metody ###############

    /**
     * Přesměruje na home stránku. Řídí se konfigurací. Home stránka je definována jménem bloku nebo jménem statické stránky nebo
     * identifikátorem uid položky menu (položky hierarchie).
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    public function home(ServerRequestInterface $request): ResponseInterface {
        $menuItem = $this->getSafeHomeItem();
        return $this->responseForItem($request, $menuItem);
    }

    public function item(ServerRequestInterface $request, $uid): ResponseInterface {
        $menuItem = $this->getSafeItem($uid);
        return $this->responseForItem($request, $menuItem);
    }
    
    public function searchResult(ServerRequestInterface $request): ResponseInterface {
        // TODO tady je nějaký zmatek
        /** @var SearchResultComponent $component */
        $component = $this->container->get(SearchResultComponent::class);
        $key = $request->getQueryParams()['klic'];
        $actionComponents = ["content" => $component->setSearch($key)];
        return $this->createStringOKResponseFromView($this->composeLayoutView($request, $this->getComponentViews($actionComponents)));
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $staticName
     * @return type
     */
    protected function staticFallback(ServerRequestInterface $request): ResponseInterface {
        $homeStaticFallback = ConfigurationCache::layoutControler()['home_static_fallback'];
        // injektování kontejneru do template
        $this->templateCompiler->injectTemplateVars([TemplateCompilerInterface::VARNAME_CONTAINER => $this->container]);
        $compiledContent = $this->templateCompiler->getCompiledContent($request, $homeStaticFallback);
        return $this->createStringOKResponse($compiledContent);
    }
}
