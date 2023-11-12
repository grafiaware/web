<?php
namespace Sendmail\Middleware\Sendmail;

use Pes\Middleware\AppMiddlewareAbstract;
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

use Sendmail\Middleware\Sendmail\Controller\MailController;

class Sendmail extends AppMiddlewareAbstract implements MiddlewareInterface {

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
            (new MailContainerConfigurator())->configure(
                (new AuthContainerConfigurator())->configure(
                    (new AuthDbContainerConfigurator())->configure(
                        new Container($this->getApp()->getAppContainer())
                    )
                )
            );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        $routeGenerator->addRouteForAction('GET', '/sendmail/v1/campaign/:campaign', function(ServerRequestInterface $request, $campaign) {
            /** @var MailController $ctrl */
            $ctrl = $this->container->get(MailController::class);
            return $ctrl->send($request, $campaign);
        });
####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


