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

use Access\Enum\RoleEnum;
use Access\Enum\AllowedActionEnum;

use Component\View\AccessComponentInterface;

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

    /**
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response = $response->withHeader('Cache-Control', 'no-cache');
        return $response;
    }

    public function injectContainer(ContainerInterface $componentContainer): FrontControlerInterface {
        $this->container = $componentContainer;
        return $this;
    }

    public function setConfiguration($configuration): FrontControlerInterface {
        throw new LogicException("Implementace kontroleru musí implementovat vlastní metodu setConfiguration.");
    }

    ### public methods ###

    /// status control ///

    public function addFlashMessage($message, $severity = FlashSeverityEnum::INFO): void {
        $this->statusFlashRepo->get()->setMessage($message, $severity);
    }

    /// create response helpers ///

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromView(ServerRequestInterface $request, ViewInterface $view): ResponseInterface {
        $stringContent = $view->getString();
        return $this->createResponseFromString($request, $stringContent);
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param ViewInterface $view
     * @return ResponseInterface
     */
    public function createResponseFromString(ServerRequestInterface $request, $stringContent): ResponseInterface {

        $response = (new ResponseFactory())->createResponse();

        ####  hlavičky  ####
        $response = $this->addHeaders($request, $response);

        ####  body  ####
        $size = $response->getBody()->write($stringContent);
        $response->getBody()->rewind();
        return $response;
    }

    /**
     * Generuje response s přesměrováním na zadanou adresu.
     *
     * @param string $restUri Relativní adresa - resource uri
     * @return Response
     */
    public function createResponseRedirectSeeOther(ServerRequestInterface $request, $restUri): ResponseInterface {
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
        return $this->createResponseRedirectSeeOther($request, $this->statusPresentationRepo->get()->getLastGetResourcePath()); // 303 See Other
    }

    /**
     * Generuje response jako přímou odpověď na POST request.
     *
     * @param type $messageText
     * @return Response
     */
    protected function okMessageResponse($messageText) {
        // vracím 200 OK - použití 204 NoContent způsobí, že v jQuery kódu .done(function(data, textStatus, jqXHR) je proměnná data undefined a ani jqXhr objekt neobsahuje vrácený text - jQuery předpokládá, že NoContent znamená NoContent
        $response = new Response();
        $response->getBody()->write($messageText);
        return $response;
    }

    // editable mode

    protected function isItemInEditableMode($itemType, $itemId) {
        $userActions = $this->statusPresentationRepo->get()->getUserActions();
        $itemAction = $userActions->getUserItemAction($itemType, $itemId);
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();

        return (
                $userActions->presentEditableContent()
                AND isset($itemAction) AND isset($loginAggregate) AND $itemAction->getEditorLoginName()==$loginAggregate->getLoginName()
                );
    }

    // permissions

    /**
     *
     * @param AccessComponentInterface $component
     * @param type $action
     * @return bool
     */
    protected function isComponentAllowed(AccessComponentInterface $component, $action): bool {
        $isAllowed = false;
        $loginAggregate = $this->statusSecurityRepo->get()->getLoginAggregate();
        $role = isset($loginAggregate) ? $loginAggregate->getCredentials()->getRole() : null;
        $logged = isset($loginAggregate) ? true : false;
        $permissions = $component->getComponentPermissions();  // !! oprávnění komponenty - Access\Enum\AllowedViewEnum
        $activeRole = $this->getActiveRole($logged, $role, $permissions);
        if (isset($activeRole)) {
            if (array_key_exists($activeRole, $permissions) AND array_key_exists($action, $permissions[$activeRole])) {
                $resource = $permissions[$activeRole][$action];
                $isAllowed = ($component instanceof $resource) ? true : false;
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
     * Pokud uživatel má nastavenu roli a tato role je definována v permissions - vrací roli uživatele.
     * Pokud uživatek nemá nastavenu roli nebo jeho role není uvedena v permissions, zjistí jestli je v permission nastavena oprávnění pro roli 'everyone!, pokud ano, vrací roli 'everyone'.
     *
     * @param type $role
     * @param type $permissions
     * @return type
     */
    private function getActiveRole($logged, $role, $permissions) {
        if (isset($role) AND array_key_exists($role, $permissions)){
            $ret = $role;
        } elseif($logged){
            $ret = RoleEnum::EVERYONE;
        } else {
            $ret = RoleEnum::ANONYMOUS;
        }
        return $ret;
    }

    protected function getComponentPermissions(): array {
        return [
            RoleEnum::SUP => [AllowedActionEnum::GET => \Component\View\StatusComponentAbstract::class, AllowedActionEnum::POST => \Component\View\StatusComponentAbstract::class],
            RoleEnum::EDITOR => [AllowedActionEnum::GET => \Component\View\StatusComponentAbstract::class, AllowedActionEnum::POST => \Component\View\StatusComponentAbstract::class],
            RoleEnum::EVERYONE => [AllowedActionEnum::GET => \Component\View\StatusComponentAbstract::class],
            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => \Component\View\StatusComponentAbstract::class]
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
