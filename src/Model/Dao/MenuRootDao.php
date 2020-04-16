<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of MenuRootDao
 *
 * @author pes2704
 */
class MenuRootDao {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    /**
     * Vrací asociativní pole.
     *
     * @param string $name Hodnota primárního klíče
     * @return array
     */
    public function get($name) {
        $sql = "SELECT name, uid_fk FROM menu_root WHERE name=:name";
        $statement = $this->dbHandler->prepare($sql);
        $statement->bindParam(':name', $name);
        $success = $statement->execute();
        $num_rows = $statement->rowCount();
        return $num_rows ? $statement->fetch(\PDO::FETCH_ASSOC) : [];
    }

    /**
     *
     * @return array
     * @throws StatementFailureException
     */
    public function find() {

        $sql = "SELECT name, uid_fk FROM menu_root";

        $statement = $this->dbHandler->prepare($sql);
        if ($statement == FALSE) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevznikl PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $success = $statement->execute();
        if (!$success) {
            $einfo = $this->dbHandler->errorInfo();
            throw new StatementFailureException($einfo[2].PHP_EOL.". Nevykonal se PDO statement z sql příkazu: $sql", $einfo[1]);
        }
        $num_rows = $statement->rowCount();
        return $num_rows ? $statement->fetchAll(\PDO::FETCH_ASSOC) : [];
    }
}
