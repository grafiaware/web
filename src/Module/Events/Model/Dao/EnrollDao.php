<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoReferenceNonuniqueTrait;
use Events\Model\Dao\EnrollDaoInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class EnrollDao extends DaoEditAbstract implements EnrollDaoInterface {

    use DaoReferenceNonuniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['login_login_name_fk', 'event_id_fk'];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
            'login'=>['login_login_name_fk'=>'login_name'],
            'event'=>['event_id_fk'=>'id']
        ][$referenceName];
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
        return $this->findByReference('login', $loginNameFk);
    }

    public function findByEventIdFk(array $eventContentIdFk ): array {
        return $this->findByReference('event', $eventContentIdFk);
    }
}
