<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Events\Middleware\Events\Controler\CompanyControler;
use Events\Middleware\Events\Controler\ComponentEventsControler;
use Events\Middleware\Events\Controler\ComponentStaticControler;
use Events\Middleware\Events\Controler\EventControler;
use Events\Middleware\Events\Controler\EventControler_2;
use Events\Middleware\Events\Controler\FilterControler;
use Events\Middleware\Events\Controler\JobControler;
use Events\Middleware\Events\Controler\MaintenanceControler;
use Events\Middleware\Events\Controler\RepresentationControler;
use Events\Middleware\Events\Controler\SynchroControler;
use Events\Middleware\Events\Controler\VisitorJobRequestControler;
use Events\Middleware\Events\Controler\VisitorProfileControler;
use FrontControler\Response\MutationResponseMode;
use StaticRegistry\Middleware\Controler\StaticRegistryControler;

/**
 * Modulový katalog API rout — jediné místo deklarace pro tento modul.
 */
final class EventsRouteCatalog implements ModuleRouteCatalogInterface {

    public static function definitions(): array {
        return [
            new RouteDefinition('GET', '/events/v1/static/registry', StaticRegistryControler::class, 'list', null),
            new RouteDefinition('GET', '/events/v1/static/registry/:menuItemId', StaticRegistryControler::class, 'get', null),
            new RouteDefinition('GET', '/events/v1/static/templates', StaticRegistryControler::class, 'templates', null),
            new RouteDefinition('GET', '/events/v1/static/:staticName', ComponentStaticControler::class, 'static', null),
            new RouteDefinition('GET', '/events/v1/component/:name', ComponentEventsControler::class, 'component', null),
            new RouteDefinition('GET', '/events/v1/data/:name', ComponentEventsControler::class, 'dataList', null),
            new RouteDefinition('GET', '/events/v1/data/:name/:id', ComponentEventsControler::class, 'dataItem', null),
            new RouteDefinition('GET', '/events/v1/data/:parentName/:parentId/:name', ComponentEventsControler::class, 'familyDataList', null),
            new RouteDefinition('GET', '/events/v1/data/:parentName/:parentId/:name/:id', ComponentEventsControler::class, 'familyDataItem', null),
            new RouteDefinition('PUT', '/events/v1/static/registry/:menuItemId', StaticRegistryControler::class, 'upsert', MutationResponseMode::MACHINE_API),
            new RouteDefinition('DELETE', '/events/v1/static/registry/:menuItemId', StaticRegistryControler::class, 'delete', MutationResponseMode::MACHINE_API),
            new RouteDefinition('POST', '/events/v1/filterjob', FilterControler::class, 'filterJob', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/cleanfilterjob', FilterControler::class, 'cleanFilterJob', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/representation', RepresentationControler::class, 'setRepresentation', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/enroll', EventControler::class, 'enroll', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company', CompanyControler::class, 'addCompany', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId', CompanyControler::class, 'updateCompany', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/remove', CompanyControler::class, 'removeCompany', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companycontact', CompanyControler::class, 'addCompanyContact', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companycontact/:companyContactId', CompanyControler::class, 'updateCompanyContact', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companycontact/:companyContactId/remove', CompanyControler::class, 'removeCompanyContact', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companyaddress', CompanyControler::class, 'addCompanyAddress', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companyaddress/:companyIdA', CompanyControler::class, 'updateCompanyAddress', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companyaddress/:companyIdA/remove', CompanyControler::class, 'removeCompanyAddress', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companyinfo', CompanyControler::class, 'addCompanyInfo', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companyinfo/:companyIdA', CompanyControler::class, 'updateCompanyInfo', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/companyinfo/:companyIdA/remove', CompanyControler::class, 'removeCompanyInfo', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/representative', CompanyControler::class, 'addRepresentative', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/representative/:loginLoginName/:companyId/remove', CompanyControler::class, 'removeRepresentative', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/visitorprofile', VisitorProfileControler::class, 'addVisitorProfile', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/visitorprofile/:loginname', VisitorProfileControler::class, 'updateVisitorProfile', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/visitorprofile/:parentId/doctype/:type', VisitorProfileControler::class, 'addupdateDocument', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/visitorprofile/:parentId/doctype/:type/remove', VisitorProfileControler::class, 'remove', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/uploadvisitorfile', VisitorProfileControler::class, 'uploadVisitorFile', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/synchro', SynchroControler::class, 'synchro', MutationResponseMode::MACHINE_API),
            new RouteDefinition('POST', '/events/v1/validateuser', SynchroControler::class, 'validateUser', MutationResponseMode::MACHINE_API),
            new RouteDefinition('POST', '/events/v1/job/:jobId/jobrequest', VisitorJobRequestControler::class, 'addRequest', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/job/:jobId/jobrequest/:loginloginname', VisitorJobRequestControler::class, 'updateRequest', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/job/:jobId/jobrequest/:loginloginname/send', VisitorJobRequestControler::class, 'sendJobRequest', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/job', JobControler::class, 'addJob', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/job/:jobId', JobControler::class, 'updateJob', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/company/:companyId/job/:jobId/remove', JobControler::class, 'removeJob', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/jobtag', JobControler::class, 'addJobTag', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/jobtag/:jobTagId', JobControler::class, 'updateJobTag', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/jobtag/:jobTagId/remove', JobControler::class, 'removeJobTag', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/job/:jobId/jobtotag', JobControler::class, 'processingJobToTag', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/vzdelani', JobControler::class, 'addPozadovaneVzdelani', null),
            new RouteDefinition('POST', '/events/v1/vzdelani/:stupen', JobControler::class, 'updatePozadovaneVzdelani', null),
            new RouteDefinition('POST', '/events/v1/vzdelani/:stupen/remove', JobControler::class, 'removePozadovaneVzdelani', null),
            new RouteDefinition('POST', '/events/v1/institution', EventControler_2::class, 'addInstitution', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institution/:institutionId', EventControler_2::class, 'updateInstitution', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institution/:institutionId/remove', EventControler_2::class, 'removeInstitution', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institutiontype', EventControler_2::class, 'addInstitutionType', null),
            new RouteDefinition('POST', '/events/v1/institutiontype/:institutionTypeId', EventControler_2::class, 'updateInstitutionType', null),
            new RouteDefinition('POST', '/events/v1/institutiontype/:institutionTypeId/remove', EventControler_2::class, 'removeInstitutionType', null),
            new RouteDefinition('POST', '/events/v1/eventcontenttype', EventControler_2::class, 'addContentType', null),
            new RouteDefinition('POST', '/events/v1/eventcontenttype/:id', EventControler_2::class, 'updateContentType', null),
            new RouteDefinition('POST', '/events/v1/eventcontenttype/:id/remove', EventControler_2::class, 'removeContentType', null),
            new RouteDefinition('POST', '/events/v1/eventcontent', EventControler_2::class, 'addContent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontent/:idContent', EventControler_2::class, 'updateContent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontent/:idContent/remove', EventControler_2::class, 'removeContent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlinkphase', EventControler_2::class, 'addEventLinkPhase', null),
            new RouteDefinition('POST', '/events/v1/eventlinkphase/:eventLinkPhaseId', EventControler_2::class, 'updateEventLinkPhase', null),
            new RouteDefinition('POST', '/events/v1/eventlinkphase/:eventLinkPhaseId/remove', EventControler_2::class, 'removeEventLinkPhase', null),
            new RouteDefinition('POST', '/events/v1/eventlink', EventControler_2::class, 'addEventLink', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlink/:eventLinkId', EventControler_2::class, 'updateEventLink', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlink/:eventLinkId/remove', EventControler_2::class, 'removeEventLink', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/maintenance/archivecompanies/:sourceVersion/:targetVersion', MaintenanceControler::class, 'archiveCompanies', MutationResponseMode::MACHINE_API),
        ];
    }
}
