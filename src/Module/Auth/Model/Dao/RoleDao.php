<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditKeyDbVerifiedInterface;
use Model\RowData\RowDataInterface;


/**
 * Description of 
 *
 * @author 
 */
class RoleDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {


    public function getPrimaryKeyAttributes(): array {
        return ['role'];
    }

    public function getAttributes(): array {
        return ['role'];
    }

    public function getTableName(): string {
        return 'role';
    }

    public function update(RowDataInterface $rowData): bool {
        return parent::update($rowData);
    }
}
