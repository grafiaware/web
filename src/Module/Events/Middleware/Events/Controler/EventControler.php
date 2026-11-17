<?php

namespace Events\Middleware\Events\Controler;

use FrontControler\FrontControlerAbstract;
use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\RequestParams;

use Site\ConfigurationCache;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Entity\InstitutionType;
use Events\Model\Repository\InstitutionTypeRepoInterface;

use Events\Model\Entity\Institution;
use Events\Model\Repository\InstitutionRepoInterface;

use Events\Model\Entity\EventContentType;
use Events\Model\Repository\EventContentTypeRepoInterface;

use Events\Model\Entity\EventContent;
use Events\Model\Repository\EventContentRepoInterface;

use Events\Model\Entity\EventLinkPhase;
use Events\Model\Repository\EventLinkPhaseRepoInterface;

use Events\Model\Entity\EventLink;
use Events\Model\Repository\EventLinkRepoInterface;

use Events\Model\Entity\Event;
use Events\Model\Entity\EventInterface;
use Events\Model\Repository\EventRepoInterface;

use Events\Model\Entity\Enroll;
use Events\Model\Repository\EnrollRepoInterface;

/**
 * Program (G0, #116): správa eventů a jejich číselníků (instituce, typy institucí, typy obsahu, obsahy, fáze odkazů,
 * odkazy, eventy) a přihlášení návštěvníka na event (enroll).
 *
 * Oprávnění ve stylu CompanyControler: akce správy jen pro roli events_administrator, enroll jen přihlášený uživatel
 * sám za sebe. Bez oprávnění se nic nezmění a zobrazí se hláška.
 */
class EventControler extends FrontControlerAbstract {

    const NULL_VALUE_nahradni = "Toto je speciální hodnota představující NULL";

    const MESSAGE_NO_PERMISSION = "Nemáte oprávnění.";
    const MESSAGE_NOT_FOUND = "Záznam nebyl nalezen.";

    private $institutionTypeRepo;
    private $institutionRepo;
    private $eventContentTypeRepo;
    private $eventContentRepo;
    private $eventLinkPhaseRepo;
    private $eventLinkRepo;
    private $eventRepo;
    private $enrollRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,

            InstitutionRepoInterface $institutionRepo,
            InstitutionTypeRepoInterface $institutionTypeRepo,
            EventContentRepoInterface $eventContentRepo,
            EventContentTypeRepoInterface $eventContentTypeRepo,
            EventLinkPhaseRepoInterface $eventLinkPhaseRepo,
            EventLinkRepoInterface $eventLinkRepo,
            EventRepoInterface $eventRepo,
            EnrollRepoInterface $enrollRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->institutionRepo = $institutionRepo;
        $this->institutionTypeRepo = $institutionTypeRepo;
        $this->eventContentRepo = $eventContentRepo;
        $this->eventContentTypeRepo = $eventContentTypeRepo;
        $this->eventLinkPhaseRepo = $eventLinkPhaseRepo;
        $this->eventLinkRepo = $eventLinkRepo;
        $this->eventRepo = $eventRepo;
        $this->enrollRepo = $enrollRepo;
    }

    //---------------------------------------------------------------------------------------------- oprávnění

    private function getStatusLoginAggregate(): ?LoginAggregateFullInterface {
        /** @var StatusSecurityRepo $statusSecurityRepo */
        $statusSecurity = $this->statusSecurityRepo->get();
        return $statusSecurity->getLoginAggregate();
    }

    private function hasAdminPermissions(?LoginAggregateFullInterface $loginAggregateCredentials = null) {
        if (isset($loginAggregateCredentials)) {
            $role = $loginAggregateCredentials->getCredentials()->getRoleFk() ?? '';
            $hasPermissions = (isset($role) AND ($role == ConfigurationCache::auth()['roleEventsAdministrator']));
        } else {
            $hasPermissions = false;
        }
        return $hasPermissions;
    }

    private function param(ServerRequestInterface $request, string $name, $default = null) {
        return (new RequestParams())->getParsedBodyParam($request, $name, $default);
    }

    /** Hodnota výběru, kde speciální hodnota NULL_VALUE_nahradni znamená „nevybráno“. */
    private function selectParam(ServerRequestInterface $request, string $name) {
        $value = $this->param($request, $name);
        return ($value === self::NULL_VALUE_nahradni || $value === '') ? null : $value;
    }

    //---------------------------------------------------------------------------------------------- typy institucí

    public function addInstitutionType(ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $institutionType = new InstitutionType();
            $institutionType->setInstitutionType($this->param($request, 'institutionType'));
            $this->institutionTypeRepo->add($institutionType);
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function updateInstitutionType(ServerRequestInterface $request, $institutionTypeId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $institutionType = $this->institutionTypeRepo->get($institutionTypeId);
            if (isset($institutionType)) {
                $institutionType->setInstitutionType($this->param($request, 'institutionType'));
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function removeInstitutionType(ServerRequestInterface $request, $institutionTypeId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $institutionType = $this->institutionTypeRepo->get($institutionTypeId);
            if (isset($institutionType)) {
                $this->institutionTypeRepo->remove($institutionType);
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    //---------------------------------------------------------------------------------------------- instituce

    public function addInstitution(ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $institution = new Institution();
            $institution->setName($this->param($request, 'institutionName'));
            $institution->setInstitutionTypeId($this->selectParam($request, 'selectInstitutionTypeId'));
            $this->institutionRepo->add($institution);
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function updateInstitution(ServerRequestInterface $request, $institutionId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $institution = $this->institutionRepo->get($institutionId);
            if (isset($institution)) {
                $institution->setName($this->param($request, 'institutionName'));
                $institution->setInstitutionTypeId($this->selectParam($request, 'selectInstitutionTypeId'));
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function removeInstitution(ServerRequestInterface $request, $institutionId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $institution = $this->institutionRepo->get($institutionId);
            if (isset($institution)) {
                $this->institutionRepo->remove($institution);
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    //---------------------------------------------------------------------------------------------- typy obsahu

    public function addContentType(ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $contentType = new EventContentType();
            $contentType->setType($this->param($request, 'type'));
            $contentType->setName($this->param($request, 'name'));
            $this->eventContentTypeRepo->add($contentType);
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function updateContentType(ServerRequestInterface $request, $id) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventContentType = $this->eventContentTypeRepo->get($id);
            if (isset($eventContentType)) {
                $eventContentType->setType($this->param($request, 'type'));
                $eventContentType->setName($this->param($request, 'name'));
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function removeContentType(ServerRequestInterface $request, $id) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventContentType = $this->eventContentTypeRepo->get($id);
            if (isset($eventContentType)) {
                $this->eventContentTypeRepo->remove($eventContentType);
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    //---------------------------------------------------------------------------------------------- obsahy eventů

    public function addContent(ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $content = new EventContent();
            $content->setTitle($this->param($request, 'title'));
            $content->setPerex($this->param($request, 'perex'));
            $content->setParty($this->param($request, 'party'));
            $content->setInstitutionIdFk($this->selectParam($request, 'selectInstitutionId'));
            $content->setEventContentTypeIdFk($this->param($request, 'selectContentTypeId'));
            $this->eventContentRepo->add($content);
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function updateContent(ServerRequestInterface $request, $idContent) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $content = $this->eventContentRepo->get($idContent);
            if (isset($content)) {
                $content->setTitle($this->param($request, 'title'));
                $content->setPerex($this->param($request, 'perex'));
                $content->setParty($this->param($request, 'party'));
                $content->setInstitutionIdFk($this->selectParam($request, 'selectInstitutionId'));
                $content->setEventContentTypeIdFk($this->param($request, 'selectContentTypeId'));
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function removeContent(ServerRequestInterface $request, $idContent) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $content = $this->eventContentRepo->get($idContent);
            if (isset($content)) {
                $this->eventContentRepo->remove($content);
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    //---------------------------------------------------------------------------------------------- fáze odkazů

    public function addEventLinkPhase(ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventLinkPhase = new EventLinkPhase();
            $eventLinkPhase->setText($this->param($request, 'eventLinkPhaseText'));
            $this->eventLinkPhaseRepo->add($eventLinkPhase);
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function updateEventLinkPhase(ServerRequestInterface $request, $eventLinkPhaseId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventLinkPhase = $this->eventLinkPhaseRepo->get($eventLinkPhaseId);
            if (isset($eventLinkPhase)) {
                $eventLinkPhase->setText($this->param($request, 'eventLinkPhaseText'));
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function removeEventLinkPhase(ServerRequestInterface $request, $eventLinkPhaseId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventLinkPhase = $this->eventLinkPhaseRepo->get($eventLinkPhaseId);
            if (isset($eventLinkPhase)) {
                $this->eventLinkPhaseRepo->remove($eventLinkPhase);
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    //---------------------------------------------------------------------------------------------- odkazy

    public function addEventLink(ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventLink = new EventLink();
            $eventLink->setShow($this->param($request, 'show') ?? 0);
            $eventLink->setHref($this->param($request, 'href'));
            $eventLink->setLinkPhaseIdFk($this->param($request, 'eventLinkPhaseId'));
            $this->eventLinkRepo->add($eventLink);
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function updateEventLink(ServerRequestInterface $request, $eventLinkId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventLink = $this->eventLinkRepo->get($eventLinkId);
            if (isset($eventLink)) {
                $eventLink->setShow($this->param($request, 'show') ?? 0);
                $eventLink->setHref($this->param($request, 'href'));
                $eventLink->setLinkPhaseIdFk($this->param($request, 'eventLinkPhaseId'));
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function removeEventLink(ServerRequestInterface $request, $eventLinkId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $eventLink = $this->eventLinkRepo->get($eventLinkId);
            if (isset($eventLink)) {
                $this->eventLinkRepo->remove($eventLink);
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    //---------------------------------------------------------------------------------------------- eventy

    public function addEvent(ServerRequestInterface $request) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $event = new Event();
            if ($this->hydrateEvent($request, $event)) {
                $this->eventRepo->add($event);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function updateEvent(ServerRequestInterface $request, $eventId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $event = $this->eventRepo->get($eventId);
            if (isset($event)) {
                $this->hydrateEvent($request, $event);
            } else {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    public function removeEvent(ServerRequestInterface $request, $eventId) {
        if ($this->hasAdminPermissions($this->getStatusLoginAggregate())) {
            $event = $this->eventRepo->get($eventId);
            if (!isset($event)) {
                $this->addFlashMessage(self::MESSAGE_NOT_FOUND);
            } elseif ($this->enrollRepo->findByEvent($eventId)) {
                $this->addFlashMessage("Event má přihlášené návštěvníky, nelze ho smazat. Zrušte zveřejnění.");
            } else {
                $this->eventRepo->remove($event);
            }
        } else {
            $this->addFlashMessage(self::MESSAGE_NO_PERMISSION);
        }
        return $this->redirectSeeLastGet($request);
    }

    /**
     * Údaje eventu z formuláře: datum (date) + čas od a do (time), obsah, zveřejnění, odkaz pro přihlášení a vstup.
     * Neplatné datum nebo čas → hláška a false (event se neuloží ani nezmění).
     */
    private function hydrateEvent(ServerRequestInterface $request, EventInterface $event): bool {
        $date = trim((string) $this->param($request, 'date', ''));
        $start = self::dateTime($date, (string) $this->param($request, 'startTime', ''));
        $end = self::dateTime($date, (string) $this->param($request, 'endTime', ''));
        if ($start === null || $end === null || $end <= $start) {
            $this->addFlashMessage("Zadejte datum a čas začátku i konce (konec po začátku), např. 10:30.");
            return false;
        }
        $contentId = $this->selectParam($request, 'selectEventContentId');
        if ($contentId === null || $this->eventContentRepo->get($contentId) === null) {
            $this->addFlashMessage("Vyberte obsah eventu.");
            return false;
        }
        $event->setStart($start);
        $event->setEnd($end);
        $event->setPublished($this->param($request, 'published') ? 1 : 0);
        $event->setEventContentIdFk($contentId);
        $event->setEnrollLinkIdFk($this->selectParam($request, 'selectEnrollLinkId'));
        $event->setEnterLinkIdFk($this->selectParam($request, 'selectEnterLinkId'));
        return true;
    }

    /** Datum „2027-01-27“ + čas „10:30“ / „10.30“ / „1030“ / „10“ → DateTime, jinak null. */
    public static function dateTime(string $date, string $time): ?\DateTime {
        $time = trim($time);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($date)) || !preg_match('/^([01]?\d|2[0-3])(?:[:.]?([0-5]\d))?$/', $time, $m)) {
            return null;
        }
        $dt = \DateTime::createFromFormat('!Y-m-d H:i', sprintf('%s %02d:%02d', trim($date), (int) $m[1], (int) ($m[2] ?? 0)));
        return ($dt && $dt->format('Y-m-d') === trim($date)) ? $dt : null;
    }

    //---------------------------------------------------------------------------------------------- přihlášení na event

    /**
     * Přihlášení na event a odhlášení (přepínač): přihlášený uživatel sám za sebe, jen na zveřejněný event,
     * který ještě neskončil. Parametr event_enroll = id eventu.
     */
    public function enroll(ServerRequestInterface $request) {
        $loginAggregate = $this->getStatusLoginAggregate();
        $eventId = $this->param($request, 'event_enroll');
        if (!isset($loginAggregate)) {
            $this->addFlashMessage("Pro přihlášení na akci se nejdřív přihlaste ke svému účtu.");
        } elseif (!isset($eventId) || null === ($event = $this->eventRepo->get($eventId)) || !$event->getPublished()) {
            $this->addFlashMessage("Akce nebyla nalezena.");
        } elseif ($event->getEnd() !== null && $event->getEnd() < new \DateTime()) {
            $this->addFlashMessage("Akce už proběhla.");
        } else {
            $loginName = $loginAggregate->getLoginName();
            $title = $this->eventTitle($event);
            $enroll = $this->enrollRepo->get($loginName, $eventId);
            if (isset($enroll)) {
                $this->enrollRepo->remove($enroll);
                $this->addFlashMessage("Odhlášeno: " . $title);
            } else {
                $enroll = new Enroll();
                $enroll->setLoginLoginNameFk($loginName)->setEventIdFk($eventId);
                $this->enrollRepo->add($enroll);
                $this->addFlashMessage("Přihlášeno: " . $title . ". Akci najdete ve svém profilu v harmonogramu.");
            }
        }
        return $this->redirectSeeLastGet($request);
    }

    private function eventTitle(EventInterface $event): string {
        $content = $event->getEventContentIdFk() !== null ? $this->eventContentRepo->get($event->getEventContentIdFk()) : null;
        return $content ? (string) $content->getTitle() : $event->getStart()->format('j. n. G:i');
    }
}
