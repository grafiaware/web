<?php

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class RegistrationDao extends DaoAbstract {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }


    public function get($loginNameFK) {
        $sql = "
            SELECT `registration`.`login_name_fk`,
                   `registration`.`password_hash`,
                   `registration`.`email`,
                   `registration`.`email_time`
            FROM
                `registration`
            WHERE
                `registration`.`login_name_fk` = :login_name_fk";

        return $this->selectOne($sql, [':login_name_fk' => $loginNameFK], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO registration (login_name_fk, password_hash, email, email_time )
                VALUES (:login_name_fk, :password_hash, :email, :email_time )";
        return $this->execInsert($sql, [':login_name_fk'=>$row['login_name_fk'],
                                        ':password_hash'=>$row['password_hash'],
                                        ':email'=>$row['email'],
                                        ':email_time'=>$row['email_time']    ]);
    }

    public function update($row) {
        $sql = "UPDATE registration SET  password_hash = :password_hash, email = :email, email_time = :email_time
                WHERE `login_name_fk` = :login_name_fk";
        return $this->execUpdate($sql, [ ':password_hash'=>$row['password_hash'],
                                         ':email'=>$row['email'],
                                         ':email_time'=>$row['email_time'],
                                         ':login_name_fk'=>$row['login_name_fk']   ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM registration WHERE `login_name_fk` = :login_name_fk";
        return $this->execDelete($sql, [':login_name_fk'=>$row['login_name_fk'] ]);
    }
}
