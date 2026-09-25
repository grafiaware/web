<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FrontControler;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Enum\FlashSeverityEnum;

use Application\Api\RouteDefinition;
use FrontControler\StatusEnum;
use FrontControler\Response\HttpResponseFactory;
use FrontControler\Response\ResponseFactoryInterface;
use FrontControler\Response\ResponseKind;
use FrontControler\Response\ResponseModePolicy;

use Access\Enum\RoleEnum;

use Component\View\ComponentInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

use Pes\Http\Request;
use Pes\Http\Helper\UriInfoInterface;
use Pes\View\View;
use Pes\View\ViewInterface;
use Pes\View\Renderer\ImplodeRenderer;
use Pes\Core\Text\Html;

use LogicException;

/**
 * Description of ControlerAbstract
 *
 * @author pes2704
 */
abstract class FrontControlerAbstract implements FrontControlerInterface {


    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var StatusSecurityRepo
     */
    protected $statusSecurityRepo;

    /**
     *
     * @var StatusFlashRepo
     */
    protected $statusFlashRepo;

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     * Skládání HTTP response (status, hlavičky, body). Viz {@see ResponseFactoryInterface}.
     *
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * Routa právě volané akce. null = bez vynucení kind (GET / mutace bez responseMode).
     */
    private ?RouteDefinition $boundRoute = null;

    /**
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo
            ) {
        $this->statusSecurityRepo = $statusSecurityRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->responseFactory = new HttpResponseFactory(
            $statusPresentationRepo,
            fn(ResponseInterface $response) => $this->addCacheHeaders($response),
            fn(ResponseInterface $response) => $this->addContentHeaders($response),
        );
    }

    /**
     * Volá {@see \Application\Api\RouteCatalogWiring} před akcí.
     * responseMode z definice se vynucuje v create* helperách.
     */
    public function bindRouteResponse(RouteDefinition $definition): void {
        $this->boundRoute = $definition;
    }

    public function injectContainer(ContainerInterface $componentContainer): FrontControlerInterface {
        $this->container = $componentContainer;
        return $this;
    }

    public function setConfiguration($configuration): FrontControlerInterface {
        throw new LogicException("Implementace kontroleru musí implementovat vlastní metodu setConfiguration.");
    }

    protected function getResponseFactory(): ResponseFactoryInterface {
        return $this->responseFactory;
    }
    
    ### protected
    
    protected function addContentHeaders(ResponseInterface $response) {
        $statusPresentation = $this->statusPresentationRepo->getClone();
        $languageCode = $statusPresentation->getLanguageCode();
        return $response->withHeader('Content-Language', $languageCode);
    }    
        
    /**
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function addCacheHeaders(ResponseInterface $response): ResponseInterface {
//        return $response->withHeader('Cache-Control', 'no-cache');
        return $response->withHeader('Cache-Control', 'max-age=100, must-revalidate');
    }

    ### protected methods ###

    /// status control ///

    protected function addFlashMessage($message, $severity = FlashSeverityEnum::INFO): void {
        $this->statusFlashRepo->get()->setMessage($message, $severity);
    }

    protected function getLoginUserName() {
        $statusecurity = $this->statusSecurityRepo->getClone();
        return $statusecurity->hasValidSecurityContext() ? $statusecurity->getLoginAggregate()->getLoginName() : '';
    }

    /// create response helpers — delegace na HttpResponseFactory ///

    /**
     * 
     * @param ViewInterface $view
     * @param type $status
     * @return ResponseInterface
     */
    protected function createStringOKResponseFromView(ViewInterface $view, $status = StatusEnum::_200_OK): ResponseInterface {
        $this->assertResponseKind(ResponseKind::HTML);
        return $this->responseFactory->createStringOKResponseFromView($view, $status);
    }

    /**
     * 
     * @param type $stringContent
     * @param type $status
     * @return ResponseInterface
     */
    protected function createStringOKResponse($stringContent, $status = StatusEnum::_200_OK): ResponseInterface {
        $this->assertResponseKind(ResponseKind::HTML);
        return $this->responseFactory->createStringOKResponse($stringContent, $status);
    }
    
    protected function createJsonOKResponse($array, $status = StatusEnum::_200_OK): ResponseInterface {
        $this->assertResponseKind(ResponseKind::JSON);
        return $this->responseFactory->createJsonOKResponse($array, $status);
    }
    
    protected function createPutNoContentResponse($status = StatusEnum::_204_NoContent): ResponseInterface {
        $this->assertResponseKind(ResponseKind::NO_CONTENT);
        return $this->responseFactory->createPutNoContentResponse($status);
    }
    
    protected function createJsonPostCreatedResponse($array, $status = StatusEnum::_201_Created): ResponseInterface {
        $this->assertResponseKind(ResponseKind::JSON);
        return $this->responseFactory->createJsonPostCreatedResponse($array, $status);
    }    
    
    protected function createUnauthorizedResponse() {
        $this->assertResponseKind(ResponseKind::UNAUTHORIZED);
        return $this->responseFactory->createUnauthorizedResponse();
    }
    
    /**
     * Generuje response s přesměrováním na zadanou adresu.
     *
     * @param string $restUri Relativní adresa - resource uri
     * @return Response
     */
    protected function createResponseRedirectSeeOther(ServerRequestInterface $request, $restUri): ResponseInterface {
        $this->assertResponseKind(ResponseKind::REDIRECT_SEE_OTHER);
        return $this->responseFactory->createResponseRedirectSeeOther($request, $restUri);
    }

    ### protected methods ###############

    /// uri info helpers ///

    /**
     * Vrací base path pro nastavení html base path
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getBasePath(ServerRequestInterface $request) {
        $basePath = $this->getUriInfo($request)->getSubdomainPath();
        return $basePath;
    }

    /**
     * Vrací relativní path pro redirect url
     * @param ServerRequestInterface $request
     * @return type
     */
    protected function getRedirectPath(ServerRequestInterface $request) {
        return $this->getUriInfo($request)->getSubdomainPath();
    }

    /**
     * Pomocná metoda - získá base path z objektu UriInfo, který byl vložen do requestu jako atribut s jménem AppFactory::URI_INFO_ATTRIBUTE_NAME v AppFactory.
     *
     * @return UriInfoInterface
     */
    protected function getUriInfo(ServerRequestInterface $request): UriInfoInterface {
        $uriInfo = $request->getAttribute(Request::URI_INFO_ATTRIBUTE_NAME);
        if (! $uriInfo instanceof UriInfoInterface) {
            throw new LogicException("Atribut requestu ".Request::URI_INFO_ATTRIBUTE_NAME." neobsahuje objekt typu ".UriInfoInterface::class.".");
        }
        return $uriInfo;
    }

    /**
     * Generuje response s přesměrování na adresu posledního GET requestu jako odpověď na POST request při použití POST-REDIRECT-GET.
     *
     * @param ServerRequestInterface $request
     * @return type
     */
    protected function redirectSeeLastGet(ServerRequestInterface $request) {
        $this->assertResponseKind(ResponseKind::REDIRECT_SEE_OTHER);
        return $this->responseFactory->redirectSeeLastGet($request);
    }

    private function assertResponseKind(string $kind): void {
        $mode = $this->boundRoute?->responseMode;
        if ($mode === null || $mode === '') {
            return;
        }
        $allowed = ResponseModePolicy::allowedKinds($mode, $this->boundRoute->httpMethod);
        if (!in_array($kind, $allowed, true)) {
            $pattern = $this->boundRoute->httpMethod . ' ' . $this->boundRoute->urlPattern;
            throw new LogicException(
                "Response kind '{$kind}' is not allowed for responseMode '{$mode}' on {$pattern}. "
                . 'Allowed: ' . implode(', ', $allowed) . '.'
            );
        }
    }
    
    ####
    # request params
    ####

    /**
     * Vrací hodnotu POST parametru s indexem začínajicím zadaným prefixem.
     * Předpokládá, že metoda $request->getParsedBody() vrací array, nikoli objekt.
     * 
     * Pokud paremetr nenajde, vrací false
     *
     * @param ServerRequestInterface $request
     * @param type $namePrefix
     * @return string|false
     */
    protected function paramValue(ServerRequestInterface $request, $namePrefix) {
        $postParams = $request->getParsedBody();
        $params = array_filter($postParams, function($key) use ($namePrefix) {
            return strpos($key, $namePrefix)===0;
        }, ARRAY_FILTER_USE_KEY);
        return count($params) ? end($params) : false;
    }

    ####
    # permissions
    ####

    /**
     *
     * @param ComponentInterface $component
     * @param type $action
     * @return bool
     */
    protected function isAllowed($action): bool {
        $isAllowed = false;
        $loginAggregate = $this->statusSecurityRepo->getClone()->getLoginAggregate();
        $role = isset($loginAggregate) ? $loginAggregate->getCredentials()->getRoleFk() : null;
        $logged = isset($loginAggregate) ? true : false;
        $permissions = $this->getActionPermissions();
        $activeRoles = $this->getActiveRoles($logged, $role, $permissions);
        $isAllowed =false;
        do {
            $activeRole = array_pop($activeRoles);  // array_pop pro prázdné pole vrací null
            if (isset($activeRole) AND array_key_exists($activeRole, $permissions) AND array_key_exists($action, $permissions[$activeRole])) {               
                $permission = $permissions[$activeRole][$action];
                if($permission instanceof \Closure) {
                    $isAllowed = (bool) $permission();
                } else {
                    $isAllowed = (bool) $permission;
                }              
            }            
        } while (count($activeRoles) AND !$isAllowed);
        return $isAllowed;
    }

    /**
     * Pro přihlášeného uživatele:
     *  - pokud uživatel má nastavenu roli a tato role je definována v permissions - vrací roli uživatele
     *  - pokud uživatel nemá nastavenu roli nebo jeho role není uvedena v permissions - vrací roli AUTHENTICATED
     *
     * Pokud uživatel není přihlášen, vrací roli ANONYMOUS
     *
     * @param type $role
     * @param type $permissions
     * @return type
     */
    private function getActiveRoles($logged, $role, $permissions) {
        if (isset($role) AND array_key_exists($role, $permissions)){
            $ret[] = $role;
        }
        if($logged){
            $ret[] = RoleEnum::AUTHENTICATED;
        } else {
            $ret[] = RoleEnum::ANONYMOUS;
        }
        return $ret;        
    }

    protected function getActionPermissions(): array {
        return [
        ];
    }

    protected function getNonPermittedContentView($viewPermission='', $authoredType='authored content') {
        $view = $this->container->get(View::class);
        $reflect = new \ReflectionClass($this);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $reflect->getShortName().": No permissions to $viewPermission for $authoredType.")]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }

}
