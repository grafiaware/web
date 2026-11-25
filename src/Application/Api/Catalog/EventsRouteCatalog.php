<?php

namespace Application\Api\Catalog;

use Application\Api\ModuleRouteCatalogInterface;
use Application\Api\RouteDefinition;
use Events\Middleware\Events\Controler\CompanyControler;
use Events\Middleware\Events\Controler\ComponentEventsControler;
use Events\Middleware\Events\Controler\ComponentStaticControler;
use Events\Middleware\Events\Controler\EventControler;
use Events\Middleware\Events\Controler\FilterControler;
use Events\Middleware\Events\Controler\JobControler;
use Events\Middleware\Events\Controler\MaintenanceControler;
use Events\Middleware\Events\Controler\RepresentationControler;
use Events\Middleware\Events\Controler\SynchroControler;
use Events\Middleware\Events\Controler\VisitorJobRequestControler;
use Events\Middleware\Events\Controler\VisitorProfileControler;
use Events\Middleware\Events\Controler\Lottery\LotteryAdminControler;
use Events\Middleware\Events\Controler\Lottery\LotteryPaperControler;
use Events\Middleware\Events\Controler\Lottery\LotteryStaffControler;
use Events\Middleware\Events\Controler\Lottery\LotteryVisitorControler;
use Events\Middleware\Events\Controler\Reservation\ReservationAdminControler;
use Events\Middleware\Events\Controler\Reservation\ReservationPublicControler;
use Events\Middleware\Events\Controler\Promo\PromoAdminControler;
use Events\Middleware\Events\Controler\Promo\PromoQrControler;
use Events\Middleware\Events\Controler\Promo\PromoVisitControler;
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
            new RouteDefinition('POST', '/events/v1/enroll', EventControler::class, 'enroll', MutationResponseMode::BROWSER_FORM, 'vp.program'),
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
            new RouteDefinition('POST', '/events/v1/vzdelani', JobControler::class, 'addPozadovaneVzdelani', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/vzdelani/:stupen', JobControler::class, 'updatePozadovaneVzdelani', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/vzdelani/:stupen/remove', JobControler::class, 'removePozadovaneVzdelani', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institution', EventControler::class, 'addInstitution', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institution/:institutionId', EventControler::class, 'updateInstitution', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institution/:institutionId/remove', EventControler::class, 'removeInstitution', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institutiontype', EventControler::class, 'addInstitutionType', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institutiontype/:institutionTypeId', EventControler::class, 'updateInstitutionType', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/institutiontype/:institutionTypeId/remove', EventControler::class, 'removeInstitutionType', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontenttype', EventControler::class, 'addContentType', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontenttype/:id', EventControler::class, 'updateContentType', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontenttype/:id/remove', EventControler::class, 'removeContentType', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontent', EventControler::class, 'addContent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontent/:idContent', EventControler::class, 'updateContent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventcontent/:idContent/remove', EventControler::class, 'removeContent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlinkphase', EventControler::class, 'addEventLinkPhase', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlinkphase/:eventLinkPhaseId', EventControler::class, 'updateEventLinkPhase', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlinkphase/:eventLinkPhaseId/remove', EventControler::class, 'removeEventLinkPhase', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlink', EventControler::class, 'addEventLink', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlink/:eventLinkId', EventControler::class, 'updateEventLink', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/eventlink/:eventLinkId/remove', EventControler::class, 'removeEventLink', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/event', EventControler::class, 'addEvent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/event/:eventId', EventControler::class, 'updateEvent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/event/:eventId/remove', EventControler::class, 'removeEvent', MutationResponseMode::BROWSER_FORM),
            new RouteDefinition('POST', '/events/v1/maintenance/archivecompanies/:sourceVersion/:targetVersion', MaintenanceControler::class, 'archiveCompanies', MutationResponseMode::MACHINE_API),
            // D1 losování (docs/vp/zadani/D1.md), přepínač vp.losovani
            new RouteDefinition('GET', '/events/v1/mycode/page', LotteryVisitorControler::class, 'myCodePage', null, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/mycode/image', LotteryVisitorControler::class, 'myCodeImage', null, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/mycode/sw', LotteryVisitorControler::class, 'serviceWorker', null, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/scan/:code', LotteryVisitorControler::class, 'scanLanding', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/join', LotteryVisitorControler::class, 'join', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery', LotteryAdminControler::class, 'index', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery', LotteryAdminControler::class, 'saveLottery', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/:id', LotteryAdminControler::class, 'detail', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id', LotteryAdminControler::class, 'saveLottery', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/round', LotteryAdminControler::class, 'saveRound', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/round/:roundId', LotteryAdminControler::class, 'saveRound', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/round/:roundId/remove', LotteryAdminControler::class, 'removeRound', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/round/:roundId/copy', LotteryAdminControler::class, 'copyRound', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/prize', LotteryAdminControler::class, 'savePrize', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/prize/:prizeId', LotteryAdminControler::class, 'savePrize', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/prize/:prizeId/remove', LotteryAdminControler::class, 'removePrize', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/question', LotteryAdminControler::class, 'saveQuestion', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/question/:questionId', LotteryAdminControler::class, 'saveQuestion', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/question/:questionId/remove', LotteryAdminControler::class, 'removeQuestion', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/:id/winners', LotteryAdminControler::class, 'winners', null, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/:id/unclaimed/csv', LotteryAdminControler::class, 'unclaimedCsv', null, 'vp.losovani'),
            // D1c (#108): papírové lístky
            new RouteDefinition('GET', '/events/v1/lottery/:id/paper', LotteryPaperControler::class, 'overview', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/paper', LotteryPaperControler::class, 'createSet', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/:id/paper/data', LotteryPaperControler::class, 'pdfData', null, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/:id/paper/csv', LotteryPaperControler::class, 'csv', null, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/:id/scanner', LotteryStaffControler::class, 'scanner', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/scan', LotteryStaffControler::class, 'scan', MutationResponseMode::MACHINE_API, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/:id/visitor', LotteryStaffControler::class, 'searchVisitor', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/:id/presence', LotteryStaffControler::class, 'presence', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('GET', '/events/v1/lottery/round/:roundId', LotteryStaffControler::class, 'roundPage', null, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/round/:roundId/draw', LotteryStaffControler::class, 'draw', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/winner/:winnerId/claimed', LotteryStaffControler::class, 'claimed', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            new RouteDefinition('POST', '/events/v1/lottery/winner/:winnerId/substitute', LotteryStaffControler::class, 'substitute', MutationResponseMode::BROWSER_FORM, 'vp.losovani'),
            // R1 (#48): rezervace míst na přednáškách pro školy (docs/vp/zadani/R1.md)
            new RouteDefinition('POST', '/events/v1/reservation/book/:code', ReservationPublicControler::class, 'book', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('GET', '/events/v1/reservation/manage/:token', ReservationPublicControler::class, 'manage', null, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/manage/:token', ReservationPublicControler::class, 'manageSave', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/manage/:token/cancel', ReservationPublicControler::class, 'manageCancel', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('GET', '/events/v1/reservation', ReservationAdminControler::class, 'index', null, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/offer', ReservationAdminControler::class, 'saveOffer', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('GET', '/events/v1/reservation/offer/:id', ReservationAdminControler::class, 'detail', null, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/offer/:id', ReservationAdminControler::class, 'saveOffer', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/offer/:id/events', ReservationAdminControler::class, 'saveOfferEvents', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/offer/:id/phones-delete', ReservationAdminControler::class, 'deletePhones', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/offer/:id/anonymize', ReservationAdminControler::class, 'anonymize', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('GET', '/events/v1/reservation/offer/:id/csv', ReservationAdminControler::class, 'csv', null, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/item/:rid', ReservationAdminControler::class, 'saveReservation', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            new RouteDefinition('POST', '/events/v1/reservation/item/:rid/cancel', ReservationAdminControler::class, 'cancelReservation', MutationResponseMode::BROWSER_FORM, 'vp.rezervace'),
            // F1 + F2 (#47): trvalé odkazy s měřením a zdroj registrace (docs/vp/zadani/F1.md)
            new RouteDefinition('GET', '/events/v1/r/:code', PromoVisitControler::class, 'visit', null, 'vp.odkazy'),
            new RouteDefinition('GET', '/events/v1/r/:code/:channel', PromoVisitControler::class, 'visit', null, 'vp.odkazy'),
            new RouteDefinition('GET', '/events/v1/source', PromoVisitControler::class, 'source', null, 'vp.odkazy'),
            new RouteDefinition('GET', '/events/v1/promo', PromoAdminControler::class, 'index', null, 'vp.odkazy'),
            new RouteDefinition('POST', '/events/v1/promo', PromoAdminControler::class, 'save', MutationResponseMode::BROWSER_FORM, 'vp.odkazy'),
            new RouteDefinition('GET', '/events/v1/promo-csv', PromoAdminControler::class, 'csv', null, 'vp.odkazy'),
            new RouteDefinition('GET', '/events/v1/promo/:id', PromoAdminControler::class, 'detail', null, 'vp.odkazy'),
            new RouteDefinition('POST', '/events/v1/promo/:id', PromoAdminControler::class, 'save', MutationResponseMode::BROWSER_FORM, 'vp.odkazy'),
            // F3 (#47): QR z QRdirectoru (SVG), QR webu PNG, podklad plakátu; kontrolér navíc vyžaduje vp.odkazy
            new RouteDefinition('POST', '/events/v1/promo/:id/qr-svg', PromoQrControler::class, 'uploadSvg', MutationResponseMode::BROWSER_FORM, 'vp.qr-plakaty'),
            new RouteDefinition('POST', '/events/v1/promo/:id/qr-svg/:sid/delete', PromoQrControler::class, 'deleteSvg', MutationResponseMode::BROWSER_FORM, 'vp.qr-plakaty'),
            new RouteDefinition('GET', '/events/v1/promo/:id/qr.png', PromoQrControler::class, 'qrPng', null, 'vp.qr-plakaty'),
            new RouteDefinition('GET', '/events/v1/promo/:id/poster', PromoQrControler::class, 'poster', null, 'vp.qr-plakaty'),
            // ověření QR: za vp.odkazy nebo vp.losovani – kontroluje kontrolér (vypnuto obojí = 404)
            new RouteDefinition('GET', '/events/v1/qr-check', PromoQrControler::class, 'qrCheck'),
        ];
    }
}
