<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;
use Pes\Database\Handler\HandlerInterface;

/**
 * Description of ComponentDao
 *
 * @author pes2704
 */
class BlockDao {

    protected $dbHandler;

    public function __construct(HandlerInterface $dbHandler) {
        $this->dbHandler = $dbHandler;
    }

    /**
     * Vrací asociativní pole.
     *
     * @param string $name Hodnota primárního klíče user
     * @return array|false
     */
    public function get($name) {
        $sql = "SELECT name, uid_fk FROM block WHERE name=:name";
        $statement = $this->dbHandler->prepare($sql);
        $statement->bindParam(':name', $name);
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     *
     * @return array
     * @throws StatementFailureException
     */
    public function findAll() {
        $sql = "SELECT name, uid_fk FROM block";
        $statement = $this->dbHandler->prepare($sql)->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
