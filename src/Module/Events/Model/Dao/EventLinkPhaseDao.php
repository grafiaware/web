<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

use \Model\Dao\DaoAutoincrementTrait;
use Model\RowData\RowDataInterface;

use Events\Model\Dao\EventLinkPhaseDaoInterface;


/**
 * Description of EventLinkPhaseDao
 *
 * @author vlse2610
 */
class EventLinkPhaseDao extends DaoEditAbstract implements EventLinkPhaseDaoInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return ['id', 'text'];
    }

    public function getTableName(): string {
        return 'event_link_phase';
    }
}
