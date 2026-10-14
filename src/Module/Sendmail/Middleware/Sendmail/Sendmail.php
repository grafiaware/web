<?php
namespace Sendmail\Middleware\Sendmail;

use Application\Api\Catalog\SendmailRouteCatalog;
use Application\Api\RouteCatalogWiring;

use Pes\Application\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\MailContainerConfigurator;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;

class Sendmail extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // middleware kontejner:
        //      nový kontejner konfigurovaný MenuContainerConfigurator
        //      -> delegát další nový kontejner konfigurovaný ApiContainerConfigurator a LoginContainerConfigurator
        //      -> delegát aplikační kontejner
        // operace s menu používají databázi z menu kontejneru (upgrade), ostatní používají starou databázi z app kontejneru (připojovací informace
        // jsou v jednotlivých kontejnerech)
        $this->container =
            (new MailContainerConfigurator())->configure(
                (new AuthContainerConfigurator())->configure(
                    (new AuthDbContainerConfigurator())->configure(
                        new Container($this->getApp()->getAppContainer())
                    )
                )
            );

        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        RouteCatalogWiring::wire(
            $routeGenerator,
            $this->container,
            SendmailRouteCatalog::definitions(),
            ['POST']
        );

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}
