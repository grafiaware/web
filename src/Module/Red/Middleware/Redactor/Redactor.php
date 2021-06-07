<?php
namespace Red\Middleware\Redactor;

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

use Red\Middleware\Redactor\Controller\{
    UserActionController, HierarchyController, EditItemController, PresentationActionController, PaperController, ContentController, TemplateController,
    FilesUploadController
};

class Redactor extends AppMiddlewareAbstract implements MiddlewareInterface {

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
        $routeGenerator->addRouteForAction('GET', '/red/v1/useraction/app/:app', function(ServerRequestInterface $request, $app) {
            /** @var UserActionController $ctrl */
            $ctrl = $this->container->get(UserActionController::class);
            return $ctrl->app($request, $app);
            });

        #### PresentationController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/language', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setLangCode($request);
        });
//        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/uid', function(ServerRequestInterface $request) {
//                /** @var PresentationActionController $ctrl */
//                $ctrl = $this->container->get(PresentationActionController::class);
//                return $ctrl->setPresentedItem($request);
//        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/edit_layout', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditLayout($request);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/edit_article', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditArticle($request);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/edit_menu', function(ServerRequestInterface $request) {
                /** @var PresentationActionController $ctrl */
                $ctrl = $this->container->get(PresentationActionController::class);
                return $ctrl->setEditMenu($request);
        });

        #### PaperController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper', function(ServerRequestInterface $request) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->create($request);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->update($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/template', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperController $ctrl */
                $ctrl = $this->container->get(PaperController::class);
                return $ctrl->updateTemplate($request, $paperId);
        });

        #### ContentController ####

        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->updateContent($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/toggle', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->toggleContent($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/actual', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->actualContent($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/up', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->up($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/down', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->down($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/add_above', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->addAbove($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/add_below', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->addBelow($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/trash', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->trash($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/restore', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->restore($request, $paperId, $contentId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/content/:contentId/delete', function(ServerRequestInterface $request, $paperId, $contentId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(ContentController::class);
                return $ctrl->delete($request, $paperId, $contentId);
        });

        #### EditItemController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu', function(ServerRequestInterface $request) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/toggle', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/title', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->title($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/type', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemController $ctrl */
                $ctrl = $this->container->get(EditItemController::class);
                return $ctrl->type($request, $menuItemId);
        });
        #### TemplateController ####

        $routeGenerator->addRouteForAction('GET', '/red/v1/papertemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateController $ctrl */
            $ctrl = $this->container->get(TemplateController::class);
            return $ctrl->papertemplate($request, $folder);
            });
        $routeGenerator->addRouteForAction('GET', '/red/v1/authortemplate/:folder/:name', function(ServerRequestInterface $request, $folder, $name) {
            /** @var TemplateController $ctrl */
            $ctrl = $this->container->get(TemplateController::class);
            return $ctrl->authorTemplate($request, $folder, $name);
            });

        #### HierarchyController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/add', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->add($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/addchild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->addchild($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/cut', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->cut($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/cutescape', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->cutEscape($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/paste', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->paste($request, $uid, $pasteduid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/pastechild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->pastechild($request, $uid, $pasteduid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/delete', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->delete($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/trash', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyController $ctrl */
            $ctrl = $this->container->get(HierarchyController::class);
            return $ctrl->trash($request, $uid);
        });

        $routeGenerator->addRouteForAction('POST', '/red/v1/upload/editorimages', function(ServerRequestInterface $request) {
            /** @var FilesUploadController $ctrl */
            $ctrl = $this->container->get(FilesUploadController::class);
            return $ctrl->uploadEditorImages($request);
        });


####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


