<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementTrait;
use Model\Dao\DaoAutoincrementKeyInterface;


/**
 * Description of EventLinkPhaseDao
 *
 * @author vlse2610
 */
class EventLinkPhaseDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface  {

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
