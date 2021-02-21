<?php

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginAggregateDao extends DaoAbstract {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }


    public function get($loginName) {
        $sql = "
            SELECT
                `login`.`login_name`,
                `credentials`.`login_name_fk`,
                `credentials`.`password_hash`,
                `credentials`.`role`,
                `credentials`.`created`,
                `credentials`.`updated`
            FROM
                `login`
                INNER JOIN
                `credentials` ON (`login`.`login_name` = `credentials`.`login_name_fk`)
            WHERE
                `login`.`login_name` = :login_name";

        return $this->selectOne($sql, [':login_name' => $loginName], TRUE);
    }
}
