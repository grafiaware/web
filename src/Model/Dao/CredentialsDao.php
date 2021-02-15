<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
     * Vrací asociativní pole s jménem a rolí uživatele - user, role. Jde o část řádky tabulky opravneni.
     *
     * @param type $loginName
     * @return array
     * @throws StatementFailureException
     */
    public function get($loginName) {
        $sql = "
            SELECT `credentials`.`login_name`,
                `credentials`.`password_hash`,
                `credentials`.`role`,
                `credentials`.`created`,
                `credentials`.`updated`
            FROM
                `credentials`
            WHERE
                `credentials`.`login_name` = :login_name";

        return $this->selectOne($sql, [':login_name' => $loginName], TRUE);
    }

    public function insert($row) {
        $sql = "INSERT INTO credentials (login_name, password_hash, role)
                VALUES (:login_name, :password_hash, :role)";
        return $this->execInsert($sql, [':login_name'=>$row['login_name'], ':password_hash'=>$row['password_hash'], ':role'=>$row['role']  ]);
    }

    public function update($row) {
        $sql = "UPDATE credentials SET password_hash = :password_hash, role = :role
                WHERE `login_name` = :login_name";
        return $this->execUpdate($sql, [':password_hash'=>$row['password_hash'], ':role'=>$row['role'],
             ':login_name'=>$row['login_name']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM credentials WHERE `login_name` = :login_name";
        return $this->execDelete($sql, [':login_name'=>$row['login_name']]);
    }
}
