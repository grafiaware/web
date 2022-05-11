<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class RegistrationDao extends DaoEditAbstract {


    public function getPrimaryKeyAttributes(): array {
        return ['login_name_fk'];
    }

    public function getAttributes(): array {
        return [
            'login_name_fk',
            'password_hash',
            'email',
            'email_time',
            'uid',
            'info',
            'created'
        ];
    }

    public function getTableName(): string {
        return 'registration';
    }

    public function getByFk($loginNameFK) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples(['login_name_fk'])));
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getByUid($uid) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples(['uid'])));
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Object LoginDao neumožňuje insertovat bez ověření duplicity klíče. Nelze vkládat metodou insert(), je nutné používat insertWithKeyVerification().");
    }
}
