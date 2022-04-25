<?php


namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoReadonlyFkInterface;
use Model\Dao\DaoReadonlyFkTrait;
/**
 * Description of UserDao
 *
 * @author pes2704
 */
class CredentialsDao extends DaoEditAbstract implements DaoReadonlyFkInterface {

    use DaoReadonlyFkTrait;

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

    public function getTableName(): string {
        return 'credentials';
    }

}
