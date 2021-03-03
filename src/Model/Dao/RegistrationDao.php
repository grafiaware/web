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
            SELECT `registration`.`login_name_FK`,
                   `registration`.`password`,
                   `registration`.`email`,
                   `registration`.`email_time`
            FROM
                `registration`
            WHERE
                `registration`.`login_name_FK` = :login_name_FK";

        return $this->selectOne($sql, [':login_name_FK' => $loginNameFK], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO registration (login_name_FK, password, email, email_time )
                VALUES (:login_name_FK, :email, :email_time )";
        return $this->execInsert($sql, [':login_name_FK'=>$row['login_name_FK'],
                                        `:password`=>$row[`password`],
                                        ':email'=>$row['email'],
                                        ':email_time'=>$row['email_time'] ]);
    }

    public function update($row) {
        $sql = "UPDATE registration SET  password = :password, email = :email, email_time = :email_time
                WHERE `login_name_FK` = :login_name_FK";
        return $this->execUpdate($sql, [ `:password`=>$row[`password`],
                                         ':email'=>$row['email'],
                                         ':email_time'=>$row['email_time'],
                                         ':login_name_FK'=>$row['login_name_FK']   ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM registration WHERE `login_name_FK` = :login_name_FK";
        return $this->execDelete($sql, [':login_name_FK'=>$row['login_name_FK'] ]);
    }
}
