<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

/**
 * Description of CompanyParameterDao
 *
 * @author vlse2610
 */
class CompanyParameterDao extends DaoEditAbstract {

    public function getPrimaryKeyAttributes(): array {
        return ['company_id'];  //primarni klic a cizi klic
    }

    public function getAttributes(): array {
        return ['company_id', 'job_limit'];
    }

    public function getTableName(): string {
        return 'company_parameter';
    }

}
