<?php

namespace Events\Model\Dao\Reservation;

use Events\Model\Dao\Lottery\LotteryQueryTrait;
use Pes\Model\Dao\DaoAutoincrementTrait;
use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;

/**
 * R1 (#48): tabulka reservation_offer – nabídka rezervací pro školy na jeden ročník (migrace events
 * 20261122000001_r1_school_reservation). Přednášky nabídky jsou v reservation_offer_event (dotazy přes rows()/exec()).
 * Deklarovaná odchylka ADR 001 (Dao + služba s poli, jako D1). Zadání docs/vp/zadani/R1.md.
 */
class ReservationOfferDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

    use DaoAutoincrementTrait;
    use LotteryQueryTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'code', 'name', 'event_date', 'status', 'booking_from', 'booking_until', 'change_until', 'created', 'updated'];
    }

    public function getTableName(): string {
        return 'reservation_offer';
    }
}
