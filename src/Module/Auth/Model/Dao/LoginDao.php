<?php

namespace Auth\Model\Dao;
use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\RowData\RowDataInterface;

/**
 * Description of UserDao
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

    public function update(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Nelze měnit unikátní identifikátor login name.");
    }
}
