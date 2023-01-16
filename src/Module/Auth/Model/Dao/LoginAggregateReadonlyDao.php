<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginAggregateReadonlyDao extends DaoAbstract {

    public function getPrimaryKeyAttributes(): array {
        return 'login_name';
    }

    public function getAttributes(): array {
        return [
            '`login`.`login_name`',
            '`credentials`.`login_name_fk`',
            '`credentials`.`password_hash`',
            '`credentials`.`role`',
            '`credentials`.`created`',
            '`credentials`.`updated`'
        ];
    }

    public function getTableName(): string {
        return '`login`
                INNER JOIN
            `credentials`
            ON (`login`.`login_name` = `credentials`.`login_name_fk`)';
    }
}
