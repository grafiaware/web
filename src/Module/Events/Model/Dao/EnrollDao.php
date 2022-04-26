<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoReadonlyFkInterface;
use Model\Dao\DaoReadonlyFkTrait;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class EnrollDao extends DaoEditAbstract implements DaoReadonlyFkInterface {

    use DaoReadonlyFkTrait;

    public function getPrimaryKeyAttribute(): array {
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

    public function findByLoginNameFk(array $loginNameFk ) {
        return $this->findByFk('login_login_name_fk', $loginNameFk);
    }

    public function findByEventIdFk(array $eventContentIdFk ) {
        return $this->findByFk('event_id_fk', $eventContentIdFk);
    }
}
