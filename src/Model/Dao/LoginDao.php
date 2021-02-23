<?php

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginDao extends DaoAbstract {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }


    public function get($loginName) {
        $sql = "
            SELECT
                `login`.`login_name`
            FROM
                `login`
            WHERE
                `login`.`login_name` = :login_name";

        return $this->selectOne($sql, [':login_name' => $loginName], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO login (login_name)
                VALUES (:login_name )";
        return $this->execInsert($sql, [':login_name'=>$row['login_name'] ]);
    }

    public function update($row) {
        $sql = "UPDATE login SET login_name = :login_name
                WHERE `login_name` = :login_name";
        return $this->execUpdate($sql, [ ':login_name'=>$row['login_name'] ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM login WHERE `login_name` = :login_name";
        return $this->execDelete($sql, [':login_name'=>$row['login_name'] ]);
    }
}
