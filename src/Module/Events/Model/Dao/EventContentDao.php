<?php

namespace Events\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditAutoincrementKeyInterface;
use Pes\Model\Dao\DaoAutoincrementTrait;

/**
 * Description of EventContentDao
 *
 * @author pes2704
 */
class EventContentDao extends DaoEditAbstract implements DaoEditAutoincrementKeyInterface {

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
            'title',
            'perex',
            'party',            
            'event_content_type_id_fk',
            'institution_id_fk'
        ];
    }

    public function getTableName(): string {
        return 'event_content';
    }

}
