<?php
namespace Middleware\Menu;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Router\RouterInterface;
use Pes\Container\Container;

use Middleware\Menu\Controller\EditMenuController;
use Middleware\Menu\Controller\DisplayController;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Container\HierarchyContainerConfigurator;
// experimentální propojení s classmap web
use Container\ComponentContainerConfigurator;
use Container\WebContainerConfigurator;

/**
 * Description of MenuApplication
 *
 * @author pes2704
 */
class Menu extends AppMiddlewareAbstract {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // middleware kontejner: Component -> delegát Menu -> delegát Web -> delegát aplikační kontejner
        // komponenty používají databázi z menu kontejneru (upgrade), web používá starou databázi
        $container =
            (new ComponentContainerConfigurator())->configure(
                (new HierarchyContainerConfigurator())->configure(
                    (new WebContainerConfigurator())->configure(
                        new Container($this->getApp()->getAppContainer())
                    )
                )
            );

        /* @var $router RouterInterface */
        $router = $container->get(RouterInterface::class);

        $router->addRoute('GET', '/menu/nav/', function() {return (new DisplayController($this->app))->content(); });
        $router->addRoute('GET', '/menu/nav/:id/', function() {});   // subtree
        $router->addRoute('GET', '/menu/', function() {return (new DisplayController($this->app))->content(); });
        $router->addRoute('GET', '/menu/item/:id/', function($id) {return (new DisplayController($this->app))->content($id); });   // detail

        $router->addRoute('POST', '/menu/nodes/:id/', function($id) {return (new EditMenuController($this->app))->post($id); });
        /* non REST editační volání */
        $router->addRoute('POST', '/menu/add/:id/', function($id) {return (new EditMenuController($this->app))->add($id); });
        $router->addRoute('POST', '/menu/addchild/:id/', function($id) {return (new EditMenuController($this->app))->addchild($id); });
        $router->addRoute('POST', '/menu/delete/:id/', function($id) {return (new EditMenuController($this->app))->delete($id); });
        $router->addRoute('POST', '/menu/move/:id/parent/:parentid/', function() {});


        return $router->route($request) ;
    }
}
