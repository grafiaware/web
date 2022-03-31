<?php

namespace Auth\Model\Dao;
use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoTableAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\RowData\RowDataInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginDao extends DaoTableAbstract implements DaoKeyDbVerifiedInterface {

    private $keyAttribute = 'login_name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function get($loginName) {
        $select = $this->select("`login`.`login_name`");
        $from = $this->from("`login`");
        $where = $this->where("`login`.`login_name` = :login_name");
        $touplesToBind = [':login_name' => $loginName];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function findAll() {
        $select = $this->select("`login`.`login_name`");
        $from = $this->from("`login`");
        return $this->selectMany($select, $from, $where, []);
    }

    public function insertWithKeyVerification($rowData) {
        $this->execInsertWithKeyVerification('login', ['login_name'], $rowData);
    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Object LoginDao neumožňuje insertovat bez ověření duplicity klíče. Nelze vkládat metodou insert(), je nutné používat insertWithKeyVerification().");
    }

    public function update(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Nelze měnit unikátní identifikátor login name.");
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('login', ['login_name'], $rowData);
    }
}
