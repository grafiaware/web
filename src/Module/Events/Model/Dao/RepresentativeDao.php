<?php

namespace Events\Model\Dao;

use Events\Model\Dao\RepresentativeDaoInterface;
use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoReferenceNonuniqueTrait;

/**
 * Description of RepresentativeDao
 *
 * @author vlse2610
 */
class RepresentativeDao extends DaoEditAbstract implements RepresentativeDaoInterface {

    use DaoReferenceNonuniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return [ 'login_login_name', 'company_id'];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
            'company'=>['company_id'=>'id'],
            'login'=>['login_login_name'=>'login_name']

        ][$referenceName];
    }

    public function getAttributes(): array {
        return [
            'company_id',
            'login_login_name'
        ];
    }

    public function getTableName(): string {
        return 'representative';
    }

    public function findByCompanyIdFk(array $companyIdFk): array {
        return $this->findByReference('company', $companyIdFk);
    }
}
