<?php
namespace Middleware\Web;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Action\Registry;
use Pes\Action\Action;
use Pes\Action\Resource;
use Pes\Router\RouterInterface;
use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;

use Pes\Acl\ResourcePrefix;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\WebContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Container\ComponentContainerConfigurator;

use Model\Repository\{
    StatusSecurityRepo, StatusPresentationRepo
};

use StatusManager\{
    StatusSecurityManagerInterface, StatusPresentationManagerInterface
};

use Middleware\Web\Controller\ComponentController;

class Web extends AppMiddlewareAbstract implements MiddlewareInterface {



    /**
     * @var StatusSecurityManagerInterface
     */
    protected $statusSecurityManager;

    /**
     * @var Registry
     */
    private $registry;

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // middleware kontejner:
        //      nový kontejner konfigurovaný třemi konfigurátory: ComponentContainerConfigurator, RendererContainerConfigurator a MenuContainerConfigurator
        //      -> delagát další nový kontejner konfigurovaný WebContainerConfigurator
        //      -> v něm jako delegát aplikační kontejner
        // komponenty a menu používají databázi z menu kontejneru (upgrade), web používá starou databázi z app kontejneru
        $this->container =
            (new ComponentContainerConfigurator())->configure(
                (new HierarchyContainerConfigurator())->configure(
                    new Container(
                        (new WebContainerConfigurator())->configure(
                                new Container($this->getApp()->getAppContainer())
                        )
                    )
                )
            );

        $this->registry = new Registry(new MethodEnum(), new UrlPatternValidator());

        $this->registry->register(new Action(new Resource('GET', '/www/last'), function(ServerRequestInterface $request) {
                /** @var ComponentController $ctrl */
                $ctrl = $this->container->get(ComponentController::class);
                return $ctrl->last($request);
            }));
        $this->registry->register(new Action(new Resource('GET', '/www/item/:langCode/:uid'), function(ServerRequestInterface $request, $langCode, $uid) {
                /** @var ComponentController $ctrl */
                $ctrl = $this->container->get(ComponentController::class);
                return $ctrl->item($request, $langCode, $uid);
            }));
        $this->registry->register(new Action(new Resource('GET', '/www/searchresult'), function(ServerRequestInterface $request) {
                /** @var ComponentController $ctrl */
                $ctrl = $this->container->get(ComponentController::class);
                return $ctrl->searchResult($request);
            }));
        $this->registry->register(new Action(new Resource('GET', '/'), function(ServerRequestInterface $request) {
                /** @var ComponentController $ctrl */
                $ctrl = $this->container->get(ComponentController::class);
                return $ctrl->home($request);
            }));

####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);

        /** @var Action $action */
        foreach ($this->registry as $action) {
            $router->addRoute($action->getResource(), $action->getActionCallable());
        }

        return $router->process($request, $handler) ;

//        return $router->route($request) ;
    }
}


