<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoFkNonuniqueTrait;
use Events\Model\Dao\EnrollDaoInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class EnrollDao extends DaoEditAbstract implements EnrollDaoInterface {

    use DaoFkNonuniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['login_login_name_fk', 'event_id_fk'];
    }

    public function getForeignKeyAttributes(): array {
        return [
            'login_login_name_fk'=>['login_login_name_fk'],
            'event_id_fk'=>['event_id_fk']
        ];
    }

    public function getAttributes(): array {
        return [
            'login_login_name_fk',
            'event_id_fk'
        ];
    }

    public function getTableName(): string {
        return 'enroll';
    }

    public function findByLoginNameFk(array $loginNameFk ): array {
        return $this->findByFk('login_login_name_fk', $loginNameFk);
    }

    public function findByEventIdFk(array $eventContentIdFk ): array {
        return $this->findByFk('event_id_fk', $eventContentIdFk);
    }
}