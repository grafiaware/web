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
                   `registration`.`email`,         
                   `registration`.`email_timestamp`
            FROM
                `registration`
            WHERE
                `registration`.`login_name_FK` = :login_name_FK";

        return $this->selectOne($sql, [':login_name_FK' => $loginNameFK], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO registration (login_name_FK, email, email_timestamp )
                VALUES (:login_name_FK, :email, :email_timestamp )";
        return $this->execInsert($sql, [':login_name_FK'=>$row['login_name_FK'], 
                                        ':email'=>$row['email'], 
                                        ':email_timestamp'=>$row['email_timestamp'] ]);
    }

    public function update($row) {
        $sql = "UPDATE registration SET  email = :email, email_timestamp = :email_timestamp
                WHERE `login_name_FK` = :login_name_FK";
        return $this->execUpdate($sql, [ ':email'=>$row['email'], 
                                         ':email_timestamp'=>$row['email_timestamp'],
                                         ':login_name_FK'=>$row['login_name_FK']   ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM registration WHERE `login_name_FK` = :login_name_FK";
        return $this->execDelete($sql, [':login_name_FK'=>$row['login_name_FK'] ]);
    }
}
