<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoReferenceNonuniqueTrait;
use Events\Model\Dao\CompanyToNetworkDaoInterface;



/**
 * Description of JobToTagDao
 *
 * @author vlse2610
 */
class CompanyToNetworkDao  extends DaoEditAbstract  implements CompanyToNetworkDaoInterface {

    use DaoReferenceNonuniqueTrait;


    public function getPrimaryKeyAttributes(): array {
        return ['company_id', 'network_id'];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
            'company'=>['company_id'=>'id'],
            'network'=>['network_id'=>'id']
        ][$referenceName];
    }

    public function getAttributes(): array {
        return [
            'company_id',
            'network_id',
            'link',
            'published'
        ];
    }

    public function getTableName(): string {
        return 'company_to_network';
    }

    public function findByCompanyIdFk( array $companyIdFk ): array {
        return $this->findByReference('company', $companyIdFk);
    }

    public function findByNetworkIdFk( array $networkIdFk ) : array{
        return $this->findByReference('network', $networkIdFk);
    }
}
