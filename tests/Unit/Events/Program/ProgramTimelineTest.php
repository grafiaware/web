<?php
declare(strict_types=1);

namespace Test\Unit\Events\Program;

use Events\Middleware\Events\Controler\EventControler;
use Events\Middleware\Events\ViewModel\ProgramViewModel;
use PHPUnit\Framework\TestCase;

/**
 * G0 (#116): skládání programu z řádků eventů (filtr, řazení, dny, tlačítka) a čtení data a času ve správě eventů.
 */
final class ProgramTimelineTest extends TestCase {

    private function row(int $id, string $start, string $end, string $type = 'prednaska', array $extra = []): array {
        return $extra + [
            'eventId' => $id,
            'start' => new \DateTimeImmutable($start),
            'end' => new \DateTimeImmutable($end),
            'type' => ['type' => $type, 'name' => ucfirst($type)],
            'title' => "Event $id",
            'perex' => '',
            'party' => '',
            'institution' => ['name' => 'Úřad práce Plzeň', 'type' => 'Úřad práce'],
            'enterLink' => null,
            'enrollLink' => null,
        ];
    }

    private function options(array $o = []): array {
        return $o + ['typeCode' => null, 'loginName' => null, 'enrolling' => true, 'onlyEnrolled' => false,
            'institutionName' => null, 'includePast' => false, 'enrolledByUser' => [], 'enrolledCount' => []];
    }

    public function testGroupsByDayAndSortsByStart(): void {
        $rows = [
            $this->row(3, '2027-01-28 09:00', '2027-01-28 10:00'),
            $this->row(2, '2027-01-27 11:00', '2027-01-27 12:00', 'poradna'),
            $this->row(1, '2027-01-27 09:30', '2027-01-27 10:15'),
        ];
        $timeline = ProgramViewModel::timeline($rows, $this->options(), new \DateTimeImmutable('2027-01-01'));
        $this->assertSame(['středa 27. 1. 2027', 'čtvrtek 28. 1. 2027'], array_column($timeline, 'timelinePoint'));
        $this->assertSame([1, 2], array_column($timeline[0]['box'], 'eventId'));
        $this->assertSame('9:30', $timeline[0]['box'][0]['startTime']);
        $this->assertSame('chalkboard teacher icon', $timeline[0]['box'][0]['eventType']['icon']);
        $this->assertSame('user friends icon', $timeline[0]['box'][1]['eventType']['icon']);
    }

    public function testFilters(): void {
        $rows = [
            $this->row(1, '2027-01-27 09:00', '2027-01-27 10:00'),
            $this->row(2, '2027-01-27 11:00', '2027-01-27 12:00', 'poradna', ['institution' => ['name' => 'Firma', 'type' => '']]),
            $this->row(3, '2026-01-27 09:00', '2026-01-27 10:00'),
        ];
        $now = new \DateTimeImmutable('2027-01-27 08:00');
        $ids = fn(array $t) => array_merge(...array_map(fn($d) => array_column($d['box'], 'eventId'), $t ?: [[ 'box' => []]]));
        $this->assertSame([1, 2], $ids(ProgramViewModel::timeline($rows, $this->options(), $now)), 'proběhlé skryté');
        $this->assertSame([3, 1, 2], $ids(ProgramViewModel::timeline($rows, $this->options(['includePast' => true]), $now)));
        $this->assertSame([2], $ids(ProgramViewModel::timeline($rows, $this->options(['typeCode' => 'poradna']), $now)));
        $this->assertSame([2], $ids(ProgramViewModel::timeline($rows, $this->options(['institutionName' => 'firma']), $now)));
        $this->assertSame([1], $ids(ProgramViewModel::timeline($rows,
            $this->options(['onlyEnrolled' => true, 'loginName' => 'jana', 'enrolledByUser' => ['1' => true]]), $now)));
    }

    public function testEnrollAndEnterButtons(): void {
        $rows = [
            $this->row(1, '2027-01-27 09:00', '2027-01-27 10:00', 'prednaska', ['enterLink' => ['href' => 'https://meet.test/a', 'text' => 'Vstoupit']]),
            $this->row(2, '2027-01-27 11:00', '2027-01-27 12:00'),
        ];
        $now = new \DateTimeImmutable('2027-01-27 08:00');
        $anon = ProgramViewModel::timeline($rows, $this->options(), $now)[0]['box'];
        $this->assertFalse($anon[0]['linkButtonEnroll']['showEnroll'], 'nepřihlášený tlačítko nemá');
        $this->assertTrue($anon[0]['linkButtonEnter']['showEnter']);
        $this->assertSame('https://meet.test/a', $anon[0]['linkButtonEnter']['linkButtonAttributes']['href']);
        $this->assertFalse($anon[1]['linkButtonEnter']['showEnter']);

        $logged = ProgramViewModel::timeline($rows, $this->options(['loginName' => 'jana', 'enrolledByUser' => ['2' => true],
            'enrolledCount' => ['2' => 5]]), $now)[0]['box'];
        $this->assertTrue($logged[0]['linkButtonEnroll']['showEnroll']);
        $this->assertSame('Přihlásit se', $logged[0]['linkButtonEnroll']['linkButtonText']);
        $this->assertSame('Odhlásit se', $logged[1]['linkButtonEnroll']['linkButtonText']);
        $this->assertTrue($logged[1]['enrolled']);
        $this->assertSame(5, $logged[1]['enrolledCount']);

        $noEnrolling = ProgramViewModel::timeline($rows, $this->options(['loginName' => 'jana', 'enrolling' => false]), $now)[0]['box'];
        $this->assertFalse($noEnrolling[0]['linkButtonEnroll']['showEnroll']);

        $past = ProgramViewModel::timeline($rows, $this->options(['loginName' => 'jana', 'includePast' => true]),
            new \DateTimeImmutable('2027-01-27 10:30'))[0]['box'];
        $this->assertFalse($past[0]['linkButtonEnroll']['showEnroll'], 'na proběhlý event se nepřihlašuje');
        $this->assertFalse($past[0]['linkButtonEnter']['showEnter']);
        $this->assertTrue($past[1]['linkButtonEnroll']['showEnroll']);
    }

    public function testEventDateTimeFromForm(): void {
        $this->assertSame('2027-01-27 09:30', EventControler::dateTime('2027-01-27', '9.30')->format('Y-m-d H:i'));
        $this->assertSame('2027-01-27 10:30', EventControler::dateTime('2027-01-27', '1030')->format('Y-m-d H:i'));
        $this->assertSame('2027-01-27 12:00', EventControler::dateTime('2027-01-27', '12')->format('Y-m-d H:i'));
        $this->assertNull(EventControler::dateTime('2027-01-27', '24:00'));
        $this->assertNull(EventControler::dateTime('2027-02-30', '10:00'));
        $this->assertNull(EventControler::dateTime('27.1.2027', '10:00'));
        $this->assertNull(EventControler::dateTime('2027-01-27', ''));
    }
}
