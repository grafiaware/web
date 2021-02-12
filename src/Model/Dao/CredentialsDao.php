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
        $sql = "UPDATE credentials SET login_name = :login_name, password_hash = :password_hash, role = :role
                WHERE `login_name` = :login_name";
        return $this->execUpdate($sql, [':menu_item_id_fk'=>$row['menu_item_id_fk'], ':headline'=>$row['headline'], ':perex'=>$row['perex'], ':template'=>$row['template'], ':keywords'=>$row['keywords'], ':editor'=>$row['editor'],
             ':id'=>$row['id']]);
    }

    public function delete($row) {
        $sql = "DELETE FROM credentials WHERE `login_name` = :login_name";
        return $this->execDelete($sql, [':id'=>$row['id']]);
    }

    /**
     * Vrací asociativní pole s jménem uživatele - user. Jde o část řádky tabulky opravneni.
     *
     * @param type $loginName
     * @return array
     * @throws StatementFailureException
     */
    public function getByAuthentication($loginName, $password) {
        $sql = "SELECT login_name, role FROM credentials WHERE login_name=:login_name AND password_hash=:password_hash";
        $statement = $this->dbHandler->prepare($sql);
//        $statement = $this->dbHandler->query($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $statement->bindParam(':login_name', $loginName);
        $statement->bindParam(':password_hash', $password);
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykonal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $num_rows = $statement->rowCount();

        if ($num_rows > 1) {
            user_error("V databázové tabulce opravneni existuje duplicitní záznam login_name=$loginName", E_USER_ERROR);
        }

        return $num_rows ? $statement->fetch(\PDO::FETCH_ASSOC) : [];
    }
}
