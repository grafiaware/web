<?php


namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class CredentialsDao extends DaoAbstract {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    /**
     *
     *
     * @param type $loginName     
     */
    public function get($loginNameFK) {
        $sql = "
            SELECT `credentials`.`login_name_FK`,
                `credentials`.`password_hash`,
                `credentials`.`role`,
               
                `credentials`.`created`,
                `credentials`.`updated`
            FROM
                `credentials`
            WHERE
                `credentials`.`login_name_FK` = :login_name_FK";

        return $this->selectOne($sql, [':login_name_FK' => $loginNameFK], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO credentials (login_name_FK, password_hash, role )
                VALUES (:login_name_FK, :password_hash, :role )";
        return $this->execInsert($sql, [':login_name_FK'=>$row['login_name_FK'],
                                        ':password_hash'=>$row['password_hash'], 
                                        ':role'=>$row['role']  ]);
    }

    public function update($row) {
        $sql = "UPDATE credentials SET password_hash = :password_hash, role = :role, email = :email
                WHERE `login_name_FK` = :login_name_FK";
        return $this->execUpdate($sql, [':password_hash'=>$row['password_hash'], 
                                        ':role'=>$row['role'], 
                                        ':login_name_FK'=>$row['login_name_FK'] ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM credentials WHERE `login_name_FK` = :login_name_FK";
        return $this->execDelete($sql, [':login_name_FK'=>$row['login_name_FK'] ]);
    }
}
