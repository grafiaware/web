<?php
namespace Events\Middleware\Events;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\EventsContainerConfigurator;
use Container\EventsModelContainerConfigurator;
use Container\DbEventsContainerConfigurator;
use Container\LoginContainerConfigurator;
use Container\MailContainerConfigurator;

use Events\Middleware\Events\Controler\EventcontentControler;
use Events\Middleware\Events\Controler\EventControler;
use Events\Middleware\Events\Controler\VisitorControler;
use Events\Middleware\Events\Controler\DocumentControler;
use Events\Middleware\Events\Controler\CompanyControler;
use Events\Middleware\Events\Controler\JobControler;

class Event extends AppMiddlewareAbstract implements MiddlewareInterface {

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
        } elseif ($request->getMethod()=="POST") {
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
            (new EventsContainerConfigurator())->configure(
                (new EventsModelContainerConfigurator())->configure(
                    (new DbEventsContainerConfigurator())->configure(
                        (new Container(
//                                (new LoginContainerConfigurator())->configure(
                                    (new MailContainerConfigurator())->configure(
                                        new Container($this->getApp()->getAppContainer())
                                    )
//                                )
                            )
                        )
                    )
                )
            );

####################################
        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### ComponentController ####
        $this->routeGenerator->addRouteForAction('GET', '/events/v1/eventcontent/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var EventcontentControler $ctrl */
            $ctrl = $this->container->get(EventcontentControler::class);
            return $ctrl->static($request, $staticName);
            });
    }

    ### POST #################################

    private function prepareProcessPost() {

        // middleware kontejner:
        //      nový kontejner konfigurovaný MenuContainerConfigurator
        //      -> delegát další nový kontejner konfigurovaný ApiContainerConfigurator a LoginContainerConfigurator
        //      -> delegát aplikační kontejner
        // operace s menu používají databázi z menu kontejneru (upgrade), ostatní používají starou databázi z app kontejneru (připojovací informace
        // jsou v jednotlivých kontejnerech)

        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new EventsModelContainerConfigurator())->configure(
                    (new DbEventsContainerConfigurator())->configure(
                        (new Container(
//                                (new LoginContainerConfigurator())->configure(
                                    (new MailContainerConfigurator())->configure(
                                        new Container($this->getApp()->getAppContainer())
                                    )
//                                )
                            )
                        )
                    )
                )
            );

####################################
        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        $this->routeGenerator->addRouteForAction('POST', "/events/v1/enroll", function(ServerRequestInterface $request) {
            /** @var EventControler $ctrl */
            $ctrl = $this->container->get(EventControler::class);
            return $ctrl->enroll($request);
        });
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/visitor', function(ServerRequestInterface $request) {
            /** @var VisitorControler $ctrl */
            $ctrl = $this->container->get(VisitorControler::class);
            return $ctrl->visitor($request);
        });
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/companycontact', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->insertCompanyContact($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/companycontact/:id', function(ServerRequestInterface $request, $id) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->updateCompanyContact($request, $id);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/companycontact/:id/remove', function(ServerRequestInterface $request, $id) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->removeCompanyContact($request, $id);
        });
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/companyaddress', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->insertCompanyAddress($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/companyaddress/:id', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->updateCompanyAddress($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/companyaddress/:id/remove', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->removeCompanyAddress($request);
        });
        
        
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/representative', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->insertRepresentative($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/representative/:companyId/:loginName/remove', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->removeRepresentative($request);
        });
        
        
        
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/document/:id/remove', function(ServerRequestInterface $request, $id) {
            /** @var DocumentControler $ctrl */
            $ctrl = $this->container->get(DocumentControler::class);
            return $ctrl->remove($request, $id);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/uploadvisitorfile', function(ServerRequestInterface $request) {
            /** @var VisitorControler $ctrl */
            $ctrl = $this->container->get(VisitorControler::class);
            return $ctrl->uploadVisitorFile($request);
        });
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/jobrequest', function(ServerRequestInterface $request) {
            /** @var VisitorControler $ctrl */
            $ctrl = $this->container->get(VisitorControler::class);
            return $ctrl->jobRequest($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/sendjobrequest/:visitorLoginName/:jobId', function(ServerRequestInterface $request,  $visitorLoginName, $jobId) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->sendJobRequest($request, $visitorLoginName, $jobId);
        });
    }
}


