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

use FrontControler\StatusEnum;

use Access\Enum\RoleEnum;
use Access\Enum\AllowedActionEnum;

use Component\View\ComponentInterface;

use Red\Model\Repository\ItemActionRepo;
use Red\Model\Repository\ItemActionRepoInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;
use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response\RedirectResponse;
use Pes\Http\Response;
use Pes\View\View;
use Pes\View\ViewInterface;
use Pes\View\Renderer\ImplodeRenderer;
use Pes\Text\Html;

use LogicException;
use UnexpectedValueException;

/**
 * Description of ControllerAbstract
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
    }

    public function injectContainer(ContainerInterface $componentContainer): FrontControlerInterface {
        $this->container = $componentContainer;
        return $this;
    }

    public function setConfiguration($configuration): FrontControlerInterface {
        throw new LogicException("Implementace kontroleru musí implementovat vlastní metodu setConfiguration.");
    }
    
    /**
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function addHeaders(ResponseInterface $response): ResponseInterface {
//        return $response->withHeader('Cache-Control', 'no-cache');
        return $response->withHeader('Cache-Control', 'max-age=100, must-revalidate');
    }

    ### protected methods ###

    /// status control ///

    protected function addFlashMessage($message, $severity = FlashSeverityEnum::INFO): void {
        $this->statusFlashRepo->get()->setMessage($message, $severity);
    }

    protected function getLoginUserName() {
        return $this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName();
    }
    
    private function statusCode($statusEnumValue) {
        return (new StatusEnum())($statusEnumValue);
    }
    /// create response helpers ///

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    protected function createResponseFromView(ViewInterface $view, $status = StatusEnum::_200_OK): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $stringContent = $view->getString();
        if(is_null($stringContent)) {
            $cls = get_class($view);
            $stringContent = "No string content returned by $cls method getString().";
        }
        return $this->createResponseFromString($stringContent, $statusEnumValue);
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    protected function createResponseFromString($stringContent, $status = StatusEnum::_200_OK): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $response = (new ResponseFactory())->createResponse($statusEnumValue);

        ####  hlavičky  ####
        $response = $this->addHeaders($response);

        ####  body  ####
        $size = $response->getBody()->write($stringContent);
        $response->getBody()->rewind();
        return $response;
    }
    
    protected function createJsonGetResponse($array, $status = StatusEnum::_200_OK): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $json = $this->jsonEncode($array);
        $response = $this->createResponseFromString($json)->withStatus($statusEnumValue);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    protected function createJsonPutNoContentResponse($array, $status = StatusEnum::_204_NoContent): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $json = $this->jsonEncode($array);
        $response = $this->createResponseFromString($json)->withStatus($statusEnumValue);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    protected function createJsonPostCreatedResponse($array, $status = StatusEnum::_201_Created): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $json = $this->jsonEncode($array);
        $response = $this->createResponseFromString($json)->withStatus($statusEnumValue);
        return $response->withHeader('Content-Type', 'application/json');
    }    
    
    protected function createJsonPutOKResponse($array, $status = StatusEnum::_200_OK): ResponseInterface {
        $statusEnumValue = $this->statusCode($status);
        $json = $this->jsonEncode($array);
        $response = $this->createResponseFromString($json)->withStatus($statusEnumValue);
        return $response->withHeader('Content-Type', 'application/json');
    }    
    
    private function jsonEncode($array) {
        $json = json_encode($array);
        if ($json===false) {
            throw new UnexpectedValueException("invalid value foe creating json.");
        }
        return $json;
    }
    
    /**
     * Generuje response s přesměrováním na zadanou adresu.
     *
     * @param string $restUri Relativní adresa - resource uri
     * @return Response
     */
    protected function createResponseRedirectSeeOther(ServerRequestInterface $request, $restUri): ResponseInterface {
        $newPath = $this->getUriInfo($request)->getRootAbsolutePath().ltrim($restUri, '/');
        return RedirectResponse::withPostRedirectGet(new Response(), $newPath); // 303 See Other
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
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        if (! $uriInfo instanceof UriInfoInterface) {
            throw new LogicException("Atribut requestu ".AppFactory::URI_INFO_ATTRIBUTE_NAME." neobsahuje objekt typu ".UriInfoInterface::class.".");
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
        $lastGet = $this->statusPresentationRepo->get();
        return $this->createResponseRedirectSeeOther($request, isset($lastGet) ? $lastGet->getLastGetResourcePath() : '/'); // 303 See Other
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
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        $role = isset($loginAggregate) ? $loginAggregate->getCredentials()->getRoleFk() : null;
        $logged = isset($loginAggregate) ? true : false;
        $permissions = $this->getActionPermissions();
        $activeRole = $this->getActiveRole($logged, $role, $permissions);
        if (isset($activeRole)) {
            if (array_key_exists($activeRole, $permissions) AND array_key_exists($action, $permissions[$activeRole])) {
                $resource = $permissions[$activeRole][$action];
                $isAllowed = ($this instanceof $resource) ? true : false;
            } else {
                $isAllowed =false;
            }
        }
//            $componentClass = get_class($component);
//            $m = $logged ? "Uživatel je přihlášen s rolí '$role'." : "Uživatel není přihlášen.";
//            $message = "Neznámá oprávnění pro komponentu '$componentClass' a přidělenou aktivní roli uživatele '$activeRole'. $m";

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
    private function getActiveRole($logged, $role, $permissions) {
        if($logged){
            if (isset($role) AND array_key_exists($role, $permissions)){
                $ret = $role;
            } else {
                $ret = RoleEnum::AUTHENTICATED;
            }
        } else {
            $ret = RoleEnum::ANONYMOUS;
        }
        return $ret;
    }

    protected function getActionPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::EDITOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::AUTHENTICATED => [AllowedActionEnum::GET => self::class],
            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => self::class]
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
