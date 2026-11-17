<?php

namespace Events\Middleware\Events\ViewModel;

use Events\Model\Repository\EventRepoInterface;
use Events\Model\Repository\EventContentRepoInterface;
use Events\Model\Repository\EventContentTypeRepoInterface;
use Events\Model\Repository\InstitutionRepoInterface;
use Events\Model\Repository\InstitutionTypeRepoInterface;
use Events\Model\Repository\EventLinkRepoInterface;
use Events\Model\Repository\EventLinkPhaseRepoInterface;
use Events\Model\Repository\EnrollRepoInterface;

/**
 * G0 (#116): data programu z DB pro šablony časové osy (timeline-boxes, timeline-leafs, timecolumn).
 * Náhrada array modelu EventViewModel (online veletrh 2021) – stejný tvar dat, čte jen přes repozitáře.
 *
 * Výstup getTimeline(): list<['timelinePoint' => 'středa 27. 1. 2027', 'box' => list<box>]>, dny a eventy podle začátku.
 * Box: eventId, published, eventType{type, name, icon}, title, perex, party, institution{name, type}, startTime, endTime,
 * enrolled (přihlášený uživatel je přihlášen), enrolledCount, linkButtonEnroll{showEnroll, linkButtonAttributes,
 * linkButtonText}, linkButtonEnter{showEnter, linkButtonAttributes, linkButtonText}.
 * Hodnoty nejsou escapované – escapuje šablona.
 */
class ProgramViewModel {

    /** Ikona podle kódu typu obsahu (event_content_type.type); ostatní typy DEFAULT_ICON. */
    const ICONS = [
        'prednaska' => 'chalkboard teacher icon',
        'prezentace' => 'chalkboard teacher icon',
        'pohovor' => 'microphone icon',
        'poradna' => 'user friends icon',
    ];
    const DEFAULT_ICON = 'calendar alternate outline icon';

    const DAYS = ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'];

    public function __construct(
            private EventRepoInterface $eventRepo,
            private EventContentRepoInterface $eventContentRepo,
            private EventContentTypeRepoInterface $eventContentTypeRepo,
            private InstitutionRepoInterface $institutionRepo,
            private InstitutionTypeRepoInterface $institutionTypeRepo,
            private EventLinkRepoInterface $eventLinkRepo,
            private EventLinkPhaseRepoInterface $eventLinkPhaseRepo,
            private EnrollRepoInterface $enrollRepo
            ) {
    }

    /**
     * Program po dnech.
     *
     * @param string|null $typeCode jen eventy s obsahem tohoto typu (kód, např. 'prednaska'); null = všechny
     * @param string|null $loginName přihlášený uživatel (stav přihlášení na event, tlačítko); null = nepřihlášený
     * @param bool $enrolling zobrazit tlačítko Přihlásit / Odhlásit (jen pro přihlášeného)
     * @param bool $onlyEnrolled jen eventy, na které je $loginName přihlášen („Můj harmonogram“)
     * @param string|null $institutionName jen eventy instituce s tímto názvem (harmonogram vystavovatele)
     * @param bool $includePast i eventy, které už skončily
     */
    public function getTimeline(?string $typeCode = null, ?string $loginName = null, bool $enrolling = true,
            bool $onlyEnrolled = false, ?string $institutionName = null, bool $includePast = false,
            ?\DateTimeInterface $now = null): array {
        $now = $now ?? new \DateTimeImmutable();
        $types = [];
        foreach ($this->eventContentTypeRepo->findAll() as $type) {
            $types[$type->getId()] = ['type' => (string) $type->getType(), 'name' => (string) ($type->getName() ?: $type->getType())];
        }
        $institutionTypes = [];
        foreach ($this->institutionTypeRepo->findAll() as $institutionType) {
            $institutionTypes[$institutionType->getId()] = (string) $institutionType->getInstitutionType();
        }
        $phases = [];
        foreach ($this->eventLinkPhaseRepo->findAll() as $phase) {
            $phases[$phase->getId()] = (string) $phase->getText();
        }
        $enrolledByUser = [];
        if ($loginName !== null) {
            foreach ($this->enrollRepo->findByLoginName($loginName) as $enroll) {
                $enrolledByUser[(string) $enroll->getEventIdFk()] = true;
            }
        }
        $enrolledCount = [];
        foreach ($this->enrollRepo->findAll() as $enroll) {
            $key = (string) $enroll->getEventIdFk();
            $enrolledCount[$key] = ($enrolledCount[$key] ?? 0) + 1;
        }

        $rows = [];
        foreach ($this->eventRepo->findAll() as $event) {
            if (!$event->getPublished() || $event->getStart() === null || $event->getEventContentIdFk() === null) {
                continue;
            }
            $content = $this->eventContentRepo->get($event->getEventContentIdFk());
            if ($content === null) {
                continue;
            }
            $institution = $content->getInstitutionIdFk() !== null ? $this->institutionRepo->get($content->getInstitutionIdFk()) : null;
            $rows[] = [
                'eventId' => $event->getId(),
                'start' => $event->getStart(),
                'end' => $event->getEnd(),
                'type' => $types[$content->getEventContentTypeIdFk()] ?? ['type' => '', 'name' => ''],
                'title' => (string) $content->getTitle(),
                'perex' => (string) $content->getPerex(),
                'party' => (string) $content->getParty(),
                'institution' => $institution
                    ? ['name' => (string) $institution->getName(), 'type' => $institutionTypes[$institution->getInstitutionTypeId()] ?? '']
                    : ['name' => '', 'type' => ''],
                'enterLink' => $this->link($event->getEnterLinkIdFk(), $phases),
                'enrollLink' => $this->link($event->getEnrollLinkIdFk(), $phases),
            ];
        }
        return self::timeline($rows, [
            'typeCode' => $typeCode,
            'loginName' => $loginName,
            'enrolling' => $enrolling,
            'onlyEnrolled' => $onlyEnrolled,
            'institutionName' => $institutionName,
            'includePast' => $includePast,
            'enrolledByUser' => $enrolledByUser,
            'enrolledCount' => $enrolledCount,
        ], $now);
    }

    /** Kódy a názvy typů obsahu (pro nadpisy a filtry). */
    public function getContentTypes(): array {
        $types = [];
        foreach ($this->eventContentTypeRepo->findAll() as $type) {
            $types[(string) $type->getType()] = (string) ($type->getName() ?: $type->getType());
        }
        return $types;
    }

    private function link($linkId, array $phases): ?array {
        if ($linkId === null) {
            return null;
        }
        $link = $this->eventLinkRepo->get($linkId);
        if ($link === null || !$link->getShow() || !$link->getHref()) {
            return null;
        }
        return ['href' => (string) $link->getHref(), 'text' => $phases[$link->getLinkPhaseIdFk()] ?? 'Otevřít'];
    }

    /**
     * Čistá logika (unit test): filtr, řazení, seskupení po dnech, tlačítka.
     *
     * @param array $rows řádky z getTimeline() (start, end jako \DateTimeInterface)
     * @param array $o typeCode, loginName, enrolling, onlyEnrolled, institutionName, includePast, enrolledByUser, enrolledCount
     */
    public static function timeline(array $rows, array $o, \DateTimeInterface $now): array {
        $rows = array_filter($rows, function (array $r) use ($o, $now) {
            $eventId = (string) $r['eventId'];
            $ended = ($r['end'] ?? $r['start']) < $now;
            return (empty($o['typeCode']) || $r['type']['type'] === $o['typeCode'])
                && (empty($o['institutionName']) || mb_strtolower($r['institution']['name']) === mb_strtolower($o['institutionName']))
                && (empty($o['onlyEnrolled']) || isset($o['enrolledByUser'][$eventId]))
                && (!empty($o['includePast']) || !$ended);
        });
        usort($rows, fn($a, $b) => [$a['start']->getTimestamp(), $a['title']] <=> [$b['start']->getTimestamp(), $b['title']]);

        $days = [];
        foreach ($rows as $r) {
            $eventId = (string) $r['eventId'];
            $ended = ($r['end'] ?? $r['start']) < $now;
            $isEnrolled = isset($o['enrolledByUser'][$eventId]);
            $canEnroll = !empty($o['enrolling']) && !empty($o['loginName']) && !$ended;
            $external = $r['enterLink'] ?? $r['enrollLink'] ?? null;
            $dayKey = $r['start']->format('Y-m-d');
            $days[$dayKey]['timelinePoint'] = self::dayLabel($r['start']);
            $days[$dayKey]['box'][] = [
                'eventId' => $r['eventId'],
                'published' => true,
                'eventType' => ['type' => $r['type']['type'], 'name' => $r['type']['name'],
                    'icon' => self::ICONS[$r['type']['type']] ?? self::DEFAULT_ICON],
                'title' => $r['title'],
                'perex' => $r['perex'],
                'party' => $r['party'],
                'institution' => $r['institution'],
                'startTime' => $r['start']->format('G:i'),
                'endTime' => $r['end'] ? $r['end']->format('G:i') : '',
                'enrolled' => $isEnrolled,
                'enrolledCount' => $o['enrolledCount'][$eventId] ?? 0,
                'linkButtonEnroll' => [
                    'showEnroll' => $canEnroll,
                    'linkButtonAttributes' => ['class' => $isEnrolled ? 'ui large button' : 'ui large red button'],
                    'linkButtonText' => $isEnrolled ? 'Odhlásit se' : 'Přihlásit se',
                ],
                'linkButtonEnter' => [
                    'showEnter' => $external !== null && !$ended,
                    'linkButtonAttributes' => ['class' => 'ui large green button', 'target' => '_blank', 'rel' => 'noopener',
                        'href' => $external['href'] ?? ''],
                    'linkButtonText' => $external['text'] ?? '',
                ],
            ];
        }
        ksort($days);
        return array_values($days);
    }

    public static function dayLabel(\DateTimeInterface $day): string {
        return self::DAYS[(int) $day->format('w')] . ' ' . $day->format('j. n. Y');
    }
}
