<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class LoginDao extends DaoEditAbstract implements DaoKeyDbVerifiedInterface {

    public function getPrimaryKeyAttribute(): array {
        return ['login_name'];
    }

    public function getAttributes(): array {
        return ['login_name'];
    }

    public function getTableName(): string {
        return 'login';
    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Object neumožňuje insertovat bez ověření duplicity klíče. Nelze vkládat metodou insert(), je nutné používat insertWithKeyVerification().");
    }
}
