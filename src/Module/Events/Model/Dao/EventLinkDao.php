<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementTrait;
use Events\Model\Dao\EventLinkDaoInterface;

/**
 * Description of EventLikDao
 *
 * @author vlse2610
 */
class EventLinkDao  extends DaoEditAbstract implements EventLinkDaoInterface {

    use DaoAutoincrementTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'show',
            'href',
            'link_phase_id_fk'
        ];
    }

    public function getTableName(): string {
        return 'event_link';
    }
}
