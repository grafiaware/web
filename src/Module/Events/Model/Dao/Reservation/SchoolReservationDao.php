<?php

namespace Events\Model\Dao\Reservation;

use Events\Model\Dao\Lottery\LotteryQueryTrait;
use Pes\Model\Dao\DaoAutoincrementTrait;
use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;

/**
 * R1 (#48): tabulka school_reservation – rezervace školy na přednášku (event). Token pro správu jen jako SHA-256 hash.
 * Deklarovaná odchylka ADR 001 (Dao + služba s poli, jako D1). Zadání docs/vp/zadani/R1.md.
 */
class SchoolReservationDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

    use DaoAutoincrementTrait;
    use LotteryQueryTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'offer_id_fk', 'event_id_fk', 'status', 'pupils', 'teachers', 'school_name', 'school_place', 'field_code',
            'school_class', 'contact_email', 'contact_name', 'contact_phone', 'phone_consent_at', 'phone_deleted_at', 'note',
            'consent_at', 'login_login_name_fk', 'manage_token_hash', 'created', 'updated', 'cancelled_at', 'cancelled_by',
            'anonymized_at'];
    }

    public function getTableName(): string {
        return 'school_reservation';
    }
}
