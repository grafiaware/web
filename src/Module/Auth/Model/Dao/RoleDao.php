<?php

namespace Auth\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;
use Pes\Model\RowData\RowDataInterface;


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
