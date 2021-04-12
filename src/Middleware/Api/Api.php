<?php
namespace Middleware\Api;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\{
    ApiContainerConfigurator, HierarchyContainerConfigurator, DbUpgradeContainerConfigurator, LoginContainerConfigurator, MailContainerConfigurator
};

use Middleware\Api\ApiController\{
    UserActionController, HierarchyController, EditItemController, PresentationActionController, PaperController, ContentController, EventController,
    FilesUploadControler, VisitorDataUploadControler
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
                       (new DbUpgradeContainerConfigurator())->configure(
                            (new Container(
                                    (new LoginContainerConfigurator())->configure(
                                        (new MailContainerConfigurator())->configure(
                                            new Container($this->getApp()->getAppContainer())
                                        )
                                    )
                                )
                            )
                        )
                    )
                );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### UserActionController ####
        $routeGenerator->addRouteForAction('GET', '/api/v1/useraction/app/:app', function(ServerRequestInterface $request, $app) {
            /** @var UserActionController $ctrl */
            $ctrl = $this->container->get(UserActionController::class);
            return $ctrl->app($request, $app);
            });

        #### PresentationController ####
        $routeGenerator->addRouteForAction('POST', '/api/v1/presentation/language', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setLangCode($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/presentation/uid', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setPresentedItem($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/presentation/edit_layout', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditLayout($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/presentation/edit_article', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditArticle($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/presentation/edit_menu', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditMenu($request);
        });

        #### PaperController ####
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper', function(ServerRequestInterface $request) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->create($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/template', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->updateTemplate($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/headline', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->updateHeadline($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/perex', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->updatePerex($request, $paperId);
        });

        #### ContentController ####

        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents', function(ServerRequestInterface $request, $paperId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->add($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->updateContent($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/toggle', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->toggleContent($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/actual', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->actualContent($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/up', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->up($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/down', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->down($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/add_above', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->addAbove($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/add_below', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->addBelow($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/trash', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->trash($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/restore', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->restore($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/paper/:paperId/contents/:contentId/delete', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->delete($request, $paperId, $contentId);
        });

        #### EditItemController ####
        $routeGenerator->addRouteForAction('POST', '/api/v1/menu', function(ServerRequestInterface $request) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/menu/:menuItemUidFk/toggle', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/menu/:menuItemUidFk/title', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->title($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/menu/:menuItemUidFk/type', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->type($request, $menuItemId);
        });

        #### HierarchyController ####
        $routeGenerator->addRouteForAction('POST', '/api/v1/hierarchy/:uid/add', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->add($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/hierarchy/:uid/addchild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->addchild($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/hierarchy/:uid/cut', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->cut($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/hierarchy/:uid/paste/:pasteduid', function(ServerRequestInterface $request, $uid, $pasteduid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->paste($request, $uid, $pasteduid);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/hierarchy/:uid/pastechild/:pasteduid', function(ServerRequestInterface $request, $uid, $pasteduid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->pastechild($request, $uid, $pasteduid);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/hierarchy/:uid/delete', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->delete($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/hierarchy/:uid/trash', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->trash($request, $uid);
        });

        $routeGenerator->addRouteForAction('POST', '/api/v1/upload/editorimages', function(ServerRequestInterface $request) {
            /** @var FilesUploadControler $ctrl */
            $ctrl = $this->container->get(FilesUploadControler::class);
            return $ctrl->uploadEditorImages($request);
        });

        $routeGenerator->addRouteForAction('POST', "/api/v1/event/enroll", function(ServerRequestInterface $request) {
            /** @var EventController $ctrl */
            $ctrl = $this->container->get(EventController::class);
            return $ctrl->enroll($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/event/visitor', function(ServerRequestInterface $request) {
            /** @var VisitorDataUploadControler $ctrl */
            $ctrl = $this->container->get(VisitorDataUploadControler::class);
            return $ctrl->visitor($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/event/uploadvisitorfile', function(ServerRequestInterface $request) {
            /** @var VisitorDataUploadControler $ctrl */
            $ctrl = $this->container->get(VisitorDataUploadControler::class);
            return $ctrl->uploadTxtDocuments($request);
        });
        $routeGenerator->addRouteForAction('POST', '/api/v1/event/visitorpost', function(ServerRequestInterface $request) {
            /** @var VisitorDataUploadControler $ctrl */
            $ctrl = $this->container->get(VisitorDataUploadControler::class);
            return $ctrl->postVisitorData($request);
        });
####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


