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
class CredentialsDao {

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
        //TODO: Svpboda Provizorní řešení get podle name - není unikátní -> nutno předělat opravneni na nové tabulky a user repo a dao
//        $sql = "SELECT user AS login_name, role FROM opravneni WHERE user=:login_name";
        $sql = "SELECT login_name, role FROM credentials WHERE login_name=:login_name";
        $statement = $this->dbHandler->prepare($sql);
//        $statement = $this->dbHandler->query($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $statement->bindParam(':login_name', $loginName);
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

    /**
     * Vrací asociativní pole s jménem uživatele - user. Jde o část řádky tabulky opravneni.
     *
     * @param type $loginName
     * @return array
     * @throws StatementFailureException
     */
    public function getByAuthentication($loginName, $password) {
//        $sql = "SELECT user AS login_name, role FROM opravneni WHERE user=:login_name AND password=:password";
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
