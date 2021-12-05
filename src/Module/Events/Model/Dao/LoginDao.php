<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class LoginDao extends DaoTableAbstract implements DaoKeyDbVerifiedInterface {

    public function get($loginName) {
        $select = $this->select("`login`.`login_name`");
        $from = $this->from("`login`");
        $where = $this->where("`login`.`login_name` = :login_name");
        $touplesToBind = [':login_name' => $loginName];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function insertWithKeyVerification($rowData) {
        $this->execInsertWithKeyVerification('login', ['login_name'], $rowData);
    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Object neumožňuje insertovat bez ověření duplicity klíče. Nelze vkládat metodou insert(), je nutné používat insertWithKeyVerification().");
    }

    public function update(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Nelze měnit unikátní identifikátor login name.");
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('login', ['login_name'], $rowData);
    }
}
