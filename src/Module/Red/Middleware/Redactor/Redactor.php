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

use Container\RedGetContainerConfigurator;

use Container\RedPostContainerConfigurator;
use Container\RedModelContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;

use Red\Middleware\Redactor\Controler\ComponentControler;
use Red\Middleware\Redactor\Controler\RedStaticControler;
use Red\Middleware\Redactor\Controler\TemplateControler;
use Red\Middleware\Redactor\Controler\PresentationActionControler;
use Red\Middleware\Redactor\Controler\HierarchyControler;
use Red\Middleware\Redactor\Controler\MenuControler;
use Red\Middleware\Redactor\Controler\ItemEditControler;
use Red\Middleware\Redactor\Controler\ItemActionControler;
use Red\Middleware\Redactor\Controler\PaperControler;
use Red\Middleware\Redactor\Controler\ArticleControler;
use Red\Middleware\Redactor\Controler\SectionsControler;
use Red\Middleware\Redactor\Controler\MultipageControler;
use Red\Middleware\Redactor\Controler\FilesUploadControler;

use Red\Middleware\Redactor\Controler\Exception\UnexpectedRequestMethodException;

class Redactor extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    private $routeGenerator;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        if ($request->getMethod()=="GET") {
            $this->prepareProcessGet();
        } elseif ($request->getMethod()=="POST" || $request->getMethod()=="PUT") {
            $this->prepareProcessPost();
         } else {
            throw new UnexpectedRequestMethodException("Neznámá metoda HTTP request '{$request->getMethod()}'.");
        }

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($this->routeGenerator);
        return $router->process($request, $handler) ;
    }

#### GET ################################

    private function prepareProcessGet() {
        // middleware kontejner:
        //      nový kontejner konfigurovaný MenuContainerConfigurator
        //      -> delegát další nový kontejner konfigurovaný ApiContainerConfigurator a LoginContainerConfigurator
        //      -> delegát aplikační kontejner
        // operace s menu používají databázi z menu kontejneru (upgrade), ostatní používají starou databázi z app kontejneru (připojovací informace
        // jsou v jednotlivých kontejnerech)
        $this->container =
            (new RedGetContainerConfigurator())->configure(
                (new RedModelContainerConfigurator())->configure(
                    (new DbUpgradeContainerConfigurator())->configure(
                            new Container($this->getApp()->getAppContainer())
                    )
                )
            );

        #### dočasně - pro Transformator ##########################################################
        #
            // Nový kontejner nastaví jako kontejner aplikace - pro middleware Transformator
            $this->getApp()->setAppContainer($this->container);
        #
        ###########################################################################################

        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### StaticControler ####
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var RedStaticControler $ctrl */
            $ctrl = $this->container->get(RedStaticControler::class);
            return $ctrl->static($request, $staticName);
            });
//        $this->routeGenerator->addRouteForAction('GET', '/red/v1/staticfolded/:folderName', function(ServerRequestInterface $request, $staticName) {
//            /** @var StaticControler $ctrl */
//            $ctrl = $this->container->get(StaticControler::class);
//            return $ctrl->static($request, $staticName);
//            });
//            
        #### ComponentControler ####
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/service/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->serviceComponent($request, $name);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/component/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->component($request, $name);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/root/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->root($request, $menuItemId);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/empty/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->empty($request, $menuItemId);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/select/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->select($request, $menuItemId);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/paper/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->paper($request, $menuItemId);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/article/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->article($request, $menuItemId);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/multipage/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->multipage($request, $menuItemId);
            });
            
        #### MenuControler ####
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/presenteddriver/:uid', function(ServerRequestInterface $request, $uid) {
            /** @var MenuControler $ctrl */
            $ctrl = $this->container->get(MenuControler::class);
            return $ctrl->presentedDriver($request, $uid);
            });    
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/driver/:uid', function(ServerRequestInterface $request, $uid) {
            /** @var MenuControler $ctrl */
            $ctrl = $this->container->get(MenuControler::class);
            return $ctrl->driver($request, $uid);
            });            

        #### TemplateControler ####
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/templateslist/:type', function(ServerRequestInterface $request, $type) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->templatesList($request, $type);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/papertemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->papertemplate($request, $folder);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/articletemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->articletemplate($request, $folder);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/multipagetemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->multipagetemplate($request, $folder);
            });
        $this->routeGenerator->addRouteForAction('GET', '/red/v1/authortemplate/:name', function(ServerRequestInterface $request, $name) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->authorTemplate($request, $name);
            });

    }

    ### POST #################################

    private function prepareProcessPost() {
        $this->container =
                (new RedPostContainerConfigurator())->configure(
                    (new RedModelContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(
                            (new Container(
//                                    (new MailContainerConfigurator())->configure(
                                        new Container($this->getApp()->getAppContainer())
//                                    )
                                )
                            )
                        )
                    )
                );

        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);
        #### UserActionControler ####
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/presentation/language', function(ServerRequestInterface $request) {
                /** @var PresentationActionControler $ctrl */
                $ctrl = $this->container->get(PresentationActionControler::class);
                return $ctrl->setLangCode($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/presentation/editoraction', function(ServerRequestInterface $request) {
                /** @var PresentationActionControler $ctrl */
                $ctrl = $this->container->get(PresentationActionControler::class);
                return $ctrl->setEditorAction($request);
        });

        #### ItemActionControler ####
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/itemaction/:itemId/add', function(ServerRequestInterface $request, $itemId) {
                /** @var ItemActionControler $ctrl */
                $ctrl = $this->container->get(ItemActionControler::class);
                return $ctrl->addUserItemAction($request, $itemId);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/itemaction/:itemId/remove', function(ServerRequestInterface $request, $itemId) {
                /** @var ItemActionControler $ctrl */
                $ctrl = $this->container->get(ItemActionControler::class);
                return $ctrl->removeUserItemAction($request, $itemId);
        });
        //TODO: POST version
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/itemaction/:itemId/add', function(ServerRequestInterface $request, $itemId) {
                /** @var ItemActionControler $ctrl */
                $ctrl = $this->container->get(ItemActionControler::class);
                return $ctrl->addUserItemAction($request, $itemId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/itemaction/:itemId/remove', function(ServerRequestInterface $request, $itemId) {
                /** @var ItemActionControler $ctrl */
                $ctrl = $this->container->get(ItemActionControler::class);
                return $ctrl->removeUserItemAction($request, $itemId);
        });
        
        #### PaperControler ####
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/template', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->template($request, $paperId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/templateremove', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->templateRemove($request, $paperId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/headline', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->updateHeadline($request, $paperId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/perex', function(ServerRequestInterface $request, $paperId) {
                /** @var PaperControler $ctrl */
                $ctrl = $this->container->get(PaperControler::class);
                return $ctrl->updatePerex($request, $paperId);
        });

        #### ArticleControler ####
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/article/:articleId', function(ServerRequestInterface $request, $articleId) {
                /** @var ArticleControler $ctrl */
                $ctrl = $this->container->get(ArticleControler::class);
                return $ctrl->update($request, $articleId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/article/:articleId/template', function(ServerRequestInterface $request, $articleId) {
                /** @var ArticleControler $ctrl */
                $ctrl = $this->container->get(ArticleControler::class);
                return $ctrl->template($request, $articleId);
        });

        #### MultipageControler ####

        $this->routeGenerator->addRouteForAction('POST', '/red/v1/multipage/:multipageId/template', function(ServerRequestInterface $request, $multipageId) {
                /** @var MultipageControler $ctrl */
                $ctrl = $this->container->get(MultipageControler::class);
                return $ctrl->template($request, $multipageId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/multipage/:multipageId/templateremove', function(ServerRequestInterface $request, $multipageId) {
                /** @var MultipageControler $ctrl */
                $ctrl = $this->container->get(MultipageControler::class);
                return $ctrl->templateRemove($request, $multipageId);
        });

        #### ContentControler ####
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/paper/:paperId/section', function(ServerRequestInterface $request, $paperId) {
                /** @var ContentControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->add($request, $paperId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->update($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/toggle', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->toggle($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/actual', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->actual($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/event', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->event($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/up', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->up($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/down', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->down($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/cut', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->cut($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/copy', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->copy($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/cutescape', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->cutEscape($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/pasteabove', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->pasteAbove($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/pastebelow', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->pasteBelow($request, $sectionId);
        });



        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/addabove', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->addAbove($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/addbelow', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->addBelow($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/trash', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->trash($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/restore', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->restore($request, $sectionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/section/:sectionId/delete', function(ServerRequestInterface $request, $sectionId) {
                /** @var SectionsControler $ctrl */
                $ctrl = $this->container->get(SectionsControler::class);
                return $ctrl->delete($request, $sectionId);
        });

        #### EditItemControler ####
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/menu/:menuItemUidFk/toggle', function(ServerRequestInterface $request, $menuItemId) {
                /** @var ItemEditControler $ctrl */
                $ctrl = $this->container->get(ItemEditControler::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        //TODO: POST version
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/toggle', function(ServerRequestInterface $request, $menuItemId) {
                /** @var ItemEditControler $ctrl */
                $ctrl = $this->container->get(ItemEditControler::class);
                return $ctrl->toggle($request, $menuItemId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/title', function(ServerRequestInterface $request, $menuItemId) {
                /** @var ItemEditControler $ctrl */
                $ctrl = $this->container->get(ItemEditControler::class);
                return $ctrl->title($request, $menuItemId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/menu/:menuItemUidFk/type', function(ServerRequestInterface $request, $menuItemId) {
                /** @var ItemEditControler $ctrl */
                $ctrl = $this->container->get(ItemEditControler::class);
                return $ctrl->type($request, $menuItemId);
        });

        #### HierarchyControler ####
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/add', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->add($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/addchild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->addChild($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/cut', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->cut($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/copy', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->copy($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/cutcopyescape', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->cutEscape($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/paste', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->paste($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/pastechild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->pasteChild($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/delete', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->delete($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('PUT', '/red/v1/hierarchy/:uid/trash', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->trash($request, $uid);
        });
        //TODO: POST version
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/add', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->add($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/addchild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->addChild($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/cut', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->cut($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/copy', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->copy($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/cutcopyescape', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->cutEscape($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/paste', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->paste($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/pastechild', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->pasteChild($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/delete', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->delete($request, $uid);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/hierarchy/:uid/trash', function(ServerRequestInterface $request, $uid) {
            /** @var HierarchyControler $ctrl */
            $ctrl = $this->container->get(HierarchyControler::class);
            return $ctrl->trash($request, $uid);
        });
        
        #### FilesUploadControler ####
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/upload/image', function(ServerRequestInterface $request) {
            /** @var FilesUploadControler $ctrl */
            $ctrl = $this->container->get(FilesUploadControler::class);
            return $ctrl->imageUpload($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/red/v1/upload/attachment', function(ServerRequestInterface $request) {
            /** @var FilesUploadControler $ctrl */
            $ctrl = $this->container->get(FilesUploadControler::class);
            return $ctrl->attachmentUpload($request);
        });        
    }
}


