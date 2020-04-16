<?php
namespace Middleware\Api;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Action\{Registry, Action, Resource};
use Pes\Router\{RouterInterface, MethodEnum, UrlPatternValidator};

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\{
    ApiContainerConfigurator, HierarchyContainerConfigurator, LoginContainerConfigurator
};

use Middleware\Api\ApiController\{
    UserActionController, HierarchyController, EditItemController, PresentationController, PaperController
};

class Api extends AppMiddlewareAbstract implements MiddlewareInterface {

    ## proměnné třídy - pro dostupnost v Closure definovaných v routách ##
    private $request;

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->request = $request;

        // middleware kontejner:
        //      nový kontejner konfigurovaný MenuContainerConfigurator
        //      -> delegát další nový kontejner konfigurovaný ApiContainerConfigurator a LoginContainerConfigurator
        //      -> delegát aplikační kontejner
        // operace s menu používají databázi z menu kontejneru (upgrade), ostatní používají starou databázi z app kontejneru (připojovací informace
        // jsou v jednotlivých kontejnerech)
        $this->container =
                (new ApiContainerConfigurator())->configure(
                    (new HierarchyContainerConfigurator())->configure(
                        (new Container(
                                (new LoginContainerConfigurator())->configure(
                                    new Container($this->getApp()->getAppContainer())
                                )
                            )
                        )
                    )
                );

####################################
        $this->registry = new Registry(new MethodEnum(), new UrlPatternValidator());

// else {
//            return new Response(403);  // 403 Forbidden
//        }

        #### UserActionController ####
        $this->registry->register(new Action(new Resource('GET', '/api/v1/useraction/app/:app'), function($app) {
                /** @var UserActionController $ctrl */
                $ctrl = $this->container->get(UserActionController::class);
                return $ctrl->app($this->request, $app);
        }));

        #### PresentationController ####
        $this->registry->register(new Action(new Resource('POST', '/api/v1/presentation/language'), function() {
                /** @var PresentationController $ctrl */
                $ctrl = $this->container->get(PresentationController::class);
                return $ctrl->setLangCode($this->request, );
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/presentation/uid'), function() {
                /** @var PresentationController $ctrl */
                $ctrl = $this->container->get(PresentationController::class);
                return $ctrl->setUid($this->request);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/useraction/edit_layout'), function() {
                /** @var PresentationController $ctrl */
                $ctrl = $this->container->get(PresentationController::class)->forRequest($this->request);
                return $ctrl->setEditLayout($this->request);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/useraction/edit_article'), function() {
                /** @var PresentationController $ctrl */
                $ctrl = $this->container->get(PresentationController::class);
                return $ctrl->setEditArticle($this->request);
        }));

        #### PaperController ####
        $this->registry->register(new Action(new Resource('POST', '/api/v1/paper/:menuItemId'), function($menuItemId) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->update($this->request, $menuItemId);
        }));

        #### EditItemController ####
        $this->registry->register(new Action(new Resource('POST', '/api/v1/menu/:menuItemId/toggle'), function($menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->toggle($this->request, $menuItemId);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/menu/:menuItemId/actual/:showTime/:hideTime'), function($menuItemId, $showTime, $hideTime) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->actual($this->request, $menuItemId, $showTime, $hideTime);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/menu/:menuItemId/title'), function($menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->title($this->request, $menuItemId);
        }));

        #### HierarchyController ####
        $this->registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/add'), function($uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->add($this->request, $uid);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/addchild'), function($uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->addchild($this->request, $uid);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/delete'), function($uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->delete($this->request, $uid);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/trash'), function($uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->trash($this->request, $uid);
        }));
        $this->registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/move/:parentUid'), function($uid, $parentUid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->post($this->request, $uid, $parentUid);
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


