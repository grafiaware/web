<?php
namespace Events\Model\Dao;


use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditKeyDbVerifiedInterface;


/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class LoginDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['login_name'];
    }

    public function getAttributes(): array {
        return ['login_name'];
    }

    public function getTableName(): string {
        return 'login';
    }

}
