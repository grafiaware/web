<?php


namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoReadonlyFkUniqueInterface;
use Model\Dao\DaoReadonlyFkUniqueTrait;
/**
 * Description of UserDao
 *
 * @author pes2704
 */
class CredentialsDao extends DaoEditAbstract implements DaoReadonlyFkUniqueInterface {

    use DaoReadonlyFkUniqueTrait;

    public function getPrimaryKeyAttribute(): array {
        return [
            'login_name_fk'
        ];
    }

    public function getAttributes(): array {
        return [
            'login_name_fk',
            'password_hash',
            'role',
            'created',
            'updated'
        ];
    }

    public function getForeignKeyAttributes(): array {
        return [
            'login_name_fk' => ['login_name_fk',]
        ];
    }

    public function getTableName(): string {
        return 'credentials';
    }

}
