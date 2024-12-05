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
use Container\EventsDbContainerConfigurator;
use Container\AuthContainerConfigurator;
use Container\MailContainerConfigurator;

use Events\Middleware\Events\Controler\ComponentControler;
use Events\Middleware\Events\Controler\RepresentationControler;
use Events\Middleware\Events\Controler\EventStaticControler;
use Events\Middleware\Events\Controler\EventControler;
use Events\Middleware\Events\Controler\EventControler_2;
use Events\Middleware\Events\Controler\VisitorProfileControler;
use Events\Middleware\Events\Controler\DocumentControler;
use Events\Middleware\Events\Controler\CompanyControler;
use Events\Middleware\Events\Controler\JobControler;
use Events\Middleware\Events\Controler\VisitorJobRequestControler;


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
                    (new EventsDbContainerConfigurator())->configure(
                        (new MailContainerConfigurator())->configure(
                            new Container($this->getApp()->getAppContainer())
                        )
                    )
                )
            );

####################################
        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        $this->routeGenerator->addRouteForAction('GET', '/events/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var EventStaticControler $ctrl */
            $ctrl = $this->container->get(EventStaticControler::class);
            return $ctrl->static($request, $staticName);
            });

        #### ComponentControler ####
        $this->routeGenerator->addRouteForAction('GET', '/events/v1/component/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->component($request, $name);
            });
        $this->routeGenerator->addRouteForAction('GET', '/events/v1/data/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->dataList($request, $name);
            });
        $this->routeGenerator->addRouteForAction('GET', '/events/v1/data/:name/:id', function(ServerRequestInterface $request, $name, $id) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->data($request, $name, $id);
            });
        $this->routeGenerator->addRouteForAction('GET', '/events/v1/subdata/:name/:parentId', function(ServerRequestInterface $request, $name, $parentId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->subDataList($request, $name, $parentId);
            });
//        $this->routeGenerator->addRouteForAction('GET', '/events/v1/subdata/:name/:parentId', function(ServerRequestInterface $request, $name, $parentId) {
//            /** @var ComponentControler $ctrl */
//            $ctrl = $this->container->get(ComponentControler::class);
//            return $ctrl->subDataList($request, $name, $parentId);
//            });
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
                    (new EventsDbContainerConfigurator())->configure(
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

        $this->routeGenerator->addRouteForAction('POST', "/events/v1/representation", function(ServerRequestInterface $request) {
            /** @var RepresentationControler $ctrl */
            $ctrl = $this->container->get(RepresentationControler::class);
            return $ctrl->setRepresentation($request);
        });
       
        $this->routeGenerator->addRouteForAction('POST', "/events/v1/enroll", function(ServerRequestInterface $request) {
            /** @var EventControler $ctrl */
            $ctrl = $this->container->get(EventControler::class);
            return $ctrl->enroll($request);
        });
        
      
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->addCompany($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId', function(ServerRequestInterface $request, $idCompany) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->updateCompany($request, $idCompany);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/remove', function(ServerRequestInterface $request, $idCompany) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->removeCompany($request, $idCompany);
        });

        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/companycontact', function(ServerRequestInterface $request, $idCompany) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->addCompanyContact($request, $idCompany);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/companycontact/:companyContactId', function(ServerRequestInterface $request, $idCompany, $idCompanyContact) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->updateCompanyContact($request, $idCompany, $idCompanyContact);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/companycontact/:companyContactId/remove', function(ServerRequestInterface $request, $idCompany, $idCompanyContact) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->removeCompanyContact($request, $idCompany, $idCompanyContact);
        });
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/companyaddress', function(ServerRequestInterface $request, $idCompany) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->addCompanyAddress($request, $idCompany);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/companyaddress/:companyIdA', function(ServerRequestInterface $request, $idCompany, $idCompanyA) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->updateCompanyAddress($request, $idCompany, $idCompanyA);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/companyaddress/:companyIdA/remove', function(ServerRequestInterface $request, $idCompany,  $idCompanyA) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->removeCompanyAddress($request,  $idCompany,  $idCompanyA);
        });
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/representative', function(ServerRequestInterface $request) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->addRepresentative($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/representative/:loginLoginName/:companyId/remove', 
                                                 function(ServerRequestInterface $request, $loginLoginName, $companyId ) {
            /** @var CompanyControler $ctrl */
            $ctrl = $this->container->get(CompanyControler::class);
            return $ctrl->removeRepresentative($request, $loginLoginName, $companyId);
        });
        
      
       
        //--------------------------------------
        
        
        //add
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/visitorprofile/:parentId/doctype/:type', function(ServerRequestInterface $request, $parentId, $type) {
            /** @var VisitorProfileControler $ctrl */
            $ctrl = $this->container->get(VisitorProfileControler::class);
            return $ctrl->addDocument($request, $parentId, $type);
        });  
        //update
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/visitorprofile/:parentId/doctype/:type/:id', function(ServerRequestInterface $request, $parentId, $type, $id) {
            /** @var VisitorProfileControler $ctrl */
            $ctrl = $this->container->get(VisitorProfileControler::class);
            return $ctrl->updateDocument($request, $parentId, $type, $id);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/visitorprofile/:parentId/doctype/:type/:id/remove', function(ServerRequestInterface $request, $parentId, $type, $id) {
            /** @var VisitorProfileControler $ctrl */
            $ctrl = $this->container->get(VisitorProfileControler::class);
            return $ctrl->remove($request, $parentId, $type, $id);
        });
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/visitor', function(ServerRequestInterface $request) {
            /** @var VisitorProfileControler $ctrl */
            $ctrl = $this->container->get(VisitorProfileControler::class);
            return $ctrl->visitor($request);
        });
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/uploadvisitorfile', function(ServerRequestInterface $request) {
            /** @var VisitorProfileControler $ctrl */
            $ctrl = $this->container->get(VisitorProfileControler::class);
            return $ctrl->uploadVisitorFile($request);
        });
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/:jobId/jobrequest', function(ServerRequestInterface $request, $jobId) {
            /** @var VisitorJobRequestControler $ctrl */
            $ctrl = $this->container->get(VisitorJobRequestControler::class);
            return $ctrl->jobRequest($request, $jobId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/sendjobrequest/:visitorLoginName/:jobId', function(ServerRequestInterface $request, $visitorLoginName, $jobId) {
            /** @var VisitorJobRequestControler $ctrl */
            $ctrl = $this->container->get(VisitorJobRequestControler::class);
            return $ctrl->sendJobRequest($request, $visitorLoginName, $jobId);
        });
        
        
        //-----------------      
              
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/job' , function(ServerRequestInterface $request, $idCompany) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->addJob($request, $idCompany);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/job/:jobId' , function(ServerRequestInterface $request, $idCompany, $jobId) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->updateJob($request, $idCompany, $jobId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/company/:companyId/job/:jobId/remove' , function(ServerRequestInterface $request, $idCompany, $jobId) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->removeJob($request, $idCompany,  $jobId);
        });
        
        //------------------------------------------------
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/jobtag' , function(ServerRequestInterface $request) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->addJobTag($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/jobtag/:jobTagId' , function(ServerRequestInterface $request, $tagId) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->updateJobTag($request,  $tagId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/jobtag/:jobTagId/remove' , function(ServerRequestInterface $request, $tagId) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->removeJobTag($request,  $tagId);
        });             
       
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/jobtotag/:jobId' , function(ServerRequestInterface $request, $jobId) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->processingJobToTag($request,  $jobId);
        });
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/vzdelani' , function(ServerRequestInterface $request) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->addPozadovaneVzdelani($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/vzdelani/:stupen' , function(ServerRequestInterface $request, $stupen) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->updatePozadovaneVzdelani($request, $stupen);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/vzdelani/:stupen/remove' , function(ServerRequestInterface $request, $stupen) {
            /** @var JobControler $ctrl */
            $ctrl = $this->container->get(JobControler::class);
            return $ctrl->removePozadovaneVzdelani($request, $stupen);
        });                  
             
        ###################        
        # EventControler_2
        ###################
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/institution', function(ServerRequestInterface $request) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->addInstitution($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/institution/:institutionId', function(ServerRequestInterface $request, $institutionId) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->updateInstitution($request, $institutionId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/institution/:institutionId/remove', function(ServerRequestInterface $request, $institutionId ) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->removeInstitution($request, $institutionId);
        });
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/institutiontype', function(ServerRequestInterface $request) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->addInstitutionType($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/institutiontype/:institutionTypeId', function(ServerRequestInterface $request, $institutionTypeId) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->updateInstitutionType($request, $institutionTypeId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/institutiontype/:institutionTypeId/remove', function(ServerRequestInterface $request, $institutionTypeId ) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->removeInstitutionType($request, $institutionTypeId);
        });
        
        
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventcontenttype', function(ServerRequestInterface $request) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->addContentType($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventcontenttype/:id', function(ServerRequestInterface $request, $id) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->updateContentType($request, $id);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventcontenttype/:id/remove', function(ServerRequestInterface $request, $id ) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->removeContentType($request, $id);
        });
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventcontent', function(ServerRequestInterface $request) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->addContent($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventcontent/:idContent', function(ServerRequestInterface $request, $idContent) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->updateContent($request, $idContent);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventcontent/:idContent/remove', function(ServerRequestInterface $request, $idContent ) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->removeContent($request, $idContent);
        });
        
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventlinkphase', function(ServerRequestInterface $request) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->addEventLinkPhase($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventlinkphase/:eventLinkPhaseId', function(ServerRequestInterface $request, $eventLinkPhaseId) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->updateEventLinkPhase($request, $eventLinkPhaseId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventlinkphase/:eventLinkPhaseId/remove', function(ServerRequestInterface $request, $eventLinkPhaseId ) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->removeEventLinkPhase($request, $eventLinkPhaseId);
        });
        
         $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventlink', function(ServerRequestInterface $request) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->addEventLink($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventlink/:eventLinkId', function(ServerRequestInterface $request, $eventLinkId) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->updateEventLink($request, $eventLinkId);
        });
        $this->routeGenerator->addRouteForAction('POST', '/events/v1/eventlink/:eventLinkId/remove', function(ServerRequestInterface $request, $eventLinkId ) {
            /** @var EventControler_2 $ctrl */
            $ctrl = $this->container->get(EventControler_2::class);
            return $ctrl->removeEventLink($request, $eventLinkId);
        });
                         
        
          
    }
}


