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

use Red\Middleware\Redactor\Controler\{
    UserActionControler, HierarchyControler, EditItemControler, ItemActionControler, PaperControler, ArticleControler, SectionsControler, TemplateController, MultipageControler,
    FilesUploadControler
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
//                                    (new LoginContainerConfigurator())->configure(
                                        (new MailContainerConfigurator())->configure(
                                            new Container($this->getApp()->getAppContainer())
                                        )
//                                    )
                                )
                            )
                        )
                    )
                );
//                (new ApiContainerConfigurator())->configure(
//                            (new Container(
////                                    (new LoginContainerConfigurator())->configure(
//                                        (new MailContainerConfigurator())->configure(
//                                            new Container($this->getApp()->getAppContainer())
//                                        )
////                                    )
//                                )
//                            )
//                );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### UserActionController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/language', function(ServerRequestInterface $request) {
                /** @var UserActionControler $ctrl */
                $ctrl = $this->container->get(UserActionControler::class);
                return $ctrl->setLangCode($request);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/edit_mode', function(ServerRequestInterface $request) {
                /** @var UserActionControler $ctrl */
                $ctrl = $this->container->get(UserActionControler::class);
                return $ctrl->setEditMode($request);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/edit_content', function(ServerRequestInterface $request) {
                /** @var UserActionControler $ctrl */
                $ctrl = $this->container->get(UserActionControler::class);
                return $ctrl->setEditContent($request);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/presentation/edit_menu', function(ServerRequestInterface $request) {
                /** @var UserActionControler $ctrl */
                $ctrl = $this->container->get(UserActionControler::class);
                return $ctrl->setEditMenu($request);
        });

        #### ItemActionControler ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/itemaction/:typeFk/:itemId/add', function(ServerRequestInterface $request, $typeFk, $itemId) {
                /** @var ItemActionControler $ctrl */
                $ctrl = $this->container->get(ItemActionControler::class);
                return $ctrl->addUserItemAction($request, $typeFk, $itemId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/itemaction/:typeFk/:itemId/remove', function(ServerRequestInterface $request, $typeFk, $itemId) {
                /** @var ItemActionControler $ctrl */
                $ctrl = $this->container->get(ItemActionControler::class);
                return $ctrl->removeUserItemAction($request, $typeFk, $itemId);
        });

        #### PaperController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper', function(ServerRequestInterface $request) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->create($request);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->update($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/template', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->template($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/templateremove', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->templateRemove($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/headline', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->updateHeadline($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/perex', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->updatePerex($request, $paperId);
        });

        #### ArticleControler ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/article/:articleId', function(ServerRequestInterface $request, $articleId) {
                /** @var ArticleControler $ctrl */
                $ctrl = $this->container->get(ArticleControler::class);
                return $ctrl->update($request, $articleId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/article/:articleId/template', function(ServerRequestInterface $request, $articleId) {
                /** @var ArticleControler $ctrl */
                $ctrl = $this->container->get(ArticleControler::class);
                return $ctrl->template($request, $articleId);
        });

        #### MultipageControler ####

        $routeGenerator->addRouteForAction('POST', '/red/v1/multipage/:multipageId/template', function(ServerRequestInterface $request, $multipageId) {
                /** @var MultipageControler $ctrl */
                $ctrl = $this->container->get(MultipageControler::class);
                return $ctrl->template($request, $multipageId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/multipage/:multipageId/templateremove', function(ServerRequestInterface $request, $multipageId) {
                /** @var MultipageControler $ctrl */
                $ctrl = $this->container->get(MultipageControler::class);
                return $ctrl->templateRemove($request, $multipageId);
        });

        #### ContentController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section', function(ServerRequestInterface $request, $paperId) {
                /** @var ContentController $ctrl */
                $ctrl = $this->container->get(Controler\SectionsControler::class);
                return $ctrl->add($request, $paperId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->update($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/toggle', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->toggle($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/actual', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->actual($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/event', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->event($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/up', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->up($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/down', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->down($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/add_above', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->addAbove($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/add_below', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->addBelow($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/trash', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->trash($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/restore', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->restore($request, $paperId, $sectionId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section/:sectionId/delete', function(ServerRequestInterface $request, $paperId, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->delete($request, $paperId, $sectionId);
        });

        #### EditItemController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu', function(ServerRequestInterface $request) {
                /** @var EditItemControler $ctrl */
                $ctrl = $this->container->get(EditItemControler::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/toggle', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemControler $ctrl */
                $ctrl = $this->container->get(EditItemControler::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/title', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemControler $ctrl */
                $ctrl = $this->container->get(EditItemControler::class);
                return $ctrl->title($request, $menuItemId);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/type', function(ServerRequestInterface $request, $menuItemId) {
                /** @var EditItemControler $ctrl */
                $ctrl = $this->container->get(EditItemControler::class);
                return $ctrl->type($request, $menuItemId);
        });

        #### HierarchyController ####
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/add', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->add($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/addchild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->addchild($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/cut', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->cut($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/cutescape', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->cutEscape($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/paste', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->paste($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/pastechild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->pastechild($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/delete', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->delete($request, $uid);
        });
        $routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/trash', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->trash($request, $uid);
        });

        $routeGenerator->addRouteForAction('POST', '/red/v1/upload/editorimages', function(ServerRequestInterface $request) {
            /** @var FilesUploadControler $ctrl */
            $ctrl = $this->container->get(FilesUploadControler::class);
            return $ctrl->uploadEditorImages($request);
        });


####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


