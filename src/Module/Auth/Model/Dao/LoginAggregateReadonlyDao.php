<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginAggregateReadonlyDao extends DaoAbstract {

    public function getPrimaryKeyAttribute(): array {
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

//    public function get($loginName) {
//        $select = $this->select("
//
//            ");
//        $from = $this->from("
//            `login`
//                INNER JOIN
//            `credentials`
//            ON (`login`.`login_name` = `credentials`.`login_name_fk`)");
//        $where = $this->where("
//            `login`.`login_name` = :login_name");
//        $touplesToBind = [':login_name' => $loginName];
//        return $this->selectOne($select, $from, $where, $touplesToBind, true);
//    }
//
//
//    public function find($whereClause = "", $touplesToBind = []) {
//        $select = $this->select("
//            `login`.`login_name`,
//            `credentials`.`login_name_fk`,
//            `credentials`.`password_hash`,
//            `credentials`.`role`,
//            `credentials`.`created`,
//            `credentials`.`updated`
//            ");
//        $from = $this->from("
//            `login`
//                INNER JOIN
//            `credentials`
//            ON (`login`.`login_name` = `credentials`.`login_name_fk`)");
//        $where = $this->where($whereClause);
//        return $this->selectMany($select, $from, $where, $touplesToBind);
//    }
}
