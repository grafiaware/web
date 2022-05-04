<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;


/**
 * Description of EventContentTypeDao
 *
 * @author pes2704
 */
class EventContentTypeDao extends DaoEditAbstract implements DaoKeyDbVerifiedInterface {

    public function getPrimaryKeyAttribute(): array {
        return ['type'];
    }

    public function getAttributes(): array {
        return ['type', 'name'];
    }

    public function getTableName(): string {
        return 'event_content_type';
    }
}
