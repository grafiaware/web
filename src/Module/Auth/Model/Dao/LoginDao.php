<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditKeyDbVerifiedInterface;
use Model\RowData\RowDataInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class LoginDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {


    public function getPrimaryKeyAttributes(): array {
        return ['login_name'];
    }

    public function getAttributes(): array {
        return ['login_name'];
    }

    public function getTableName(): string {
        return 'login';
    }

    public function update(RowDataInterface $rowData): bool {
        return parent::update($rowData);
    }
}
