<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginAggregateReadonlyDao extends DaoAbstract {

    public function get($loginName) {
        $select = $this->select("
            `login`.`login_name`,
            `credentials`.`login_name_fk`,
            `credentials`.`password_hash`,
            `credentials`.`role`,
            `credentials`.`created`,
            `credentials`.`updated`
            ");
        $from = $this->from("
            `login`
                INNER JOIN
            `credentials`
            ON (`login`.`login_name` = `credentials`.`login_name_fk`)");
        $where = $this->where("
            `login`.`login_name` = :login_name");
        $touplesToBind = [':login_name' => $loginName];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
}
