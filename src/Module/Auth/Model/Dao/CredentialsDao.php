<?php


namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class CredentialsDao extends DaoEditAbstract {

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


    public function getByFk($loginNameFk) {
        $select = $this->select("
            `credentials`.`login_name_fk`,
            `credentials`.`password_hash`,
            `credentials`.`role`,
            `credentials`.`created`,
            `credentials`.`updated`
            ");
        $from = $this->from("`credentials`");
        $where = $this->where("`credentials`.`login_name_fk` = :login_name_fk");
        $touplesToBind = [':login_name_fk' => $loginNameFk];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

}
