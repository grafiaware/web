<?php
namespace Middleware\Actions;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Action\{Registry, Action, Resource};
use Pes\Router\{RouterInterface, MethodEnum, UrlPatternValidator};

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\{
    ActionsContainerConfigurator, HierarchyContainerConfigurator, LoginContainerConfigurator
};

use Middleware\Actions\ActionsController\{
    UserActionController, HierarchyController, EditItemController, PresentationActionController, PaperController
};

class Actions extends AppMiddlewareAbstract implements MiddlewareInterface {

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
                (new ActionsContainerConfigurator())->configure(
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
        /** @var Registry $registry */
        $registry = $this->container->get(Registry::class);

        #### UserActionController ####
        $registry->register(new Action(new Resource('GET', '/api/v1/useraction/app/:app'), function(ServerRequestInterface $request, $app) {
                /** @var UserActionController $ctrl */
                $ctrl = $this->container->get(UserActionController::class);
                return $ctrl->app($request, $app);
        }));

        #### PresentationController ####
        $registry->register(new Action(new Resource('POST', '/api/v1/presentation/language'), function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setLangCode($request, );
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/presentation/uid'), function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setPresentedItem($request);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/useraction/edit_layout'), function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditLayout($request);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/useraction/edit_article'), function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditArticle($request);
        }));
        #### PaperController ####
        $registry->register(new Action(new Resource('POST', '/api/v1/paper/:menuItemId/headline'), function(ServerRequestInterface $request, $menuItemId) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->updateHeadline($request, $menuItemId);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/paper/:menuItemId/content/:id'), function(ServerRequestInterface $request, $menuItemId, $id) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->updateContent($request, $menuItemId, $id);
        }));
        #### EditItemController ####
        $registry->register(new Action(new Resource('POST', '/api/v1/menu/:menuItemId/toggle'), function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->toggle($request, $menuItemId);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/menu/:menuItemId/actual'), function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->actual($request, $menuItemId);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/menu/:menuItemId/title'), function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->title($request, $menuItemId);
        }));

        #### HierarchyController ####
        $registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/add'), function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->add($request, $uid);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/addchild'), function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->addchild($request, $uid);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/delete'), function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->delete($request, $uid);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/trash'), function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->trash($request, $uid);
        }));
        $registry->register(new Action(new Resource('POST', '/api/v1/hierarchy/:uid/move/:parentUid'), function(ServerRequestInterface $request, $uid, $parentUid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->post($request, $uid, $parentUid);
        }));

####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);

        /** @var Action $action */
        foreach ($registry as $action) {
            $router->addRoute($action->getResource(), $action->getActionCallable());
        }

        return $router->process($request, $handler) ;

//        return $router->route($request) ;
    }
}

