<?php

namespace Auth\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;
use Pes\Model\RowData\RowDataInterface;

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
