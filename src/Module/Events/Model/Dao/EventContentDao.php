<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;

/**
 * Description of EventContentDao
 *
 * @author pes2704
 */
class EventContentDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'title',
            'perex',
            'party',
            'event_content_type_fk',
            'institution_id_fk'
        ];
    }

    public function getTableName(): string {
        return 'event_content';
    }

}
