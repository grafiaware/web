<?php

namespace Events\Model\Dao;

use Events\Model\Dao\RepresentativeDaoInterface;
use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoFkNonuniqueTrait;
use \Model\Dao\DaoFkUniqueTrait;

/**
 * Description of RepresentativeDao
 *
 * @author vlse2610
 */
class RepresentativeDao extends DaoEditAbstract implements RepresentativeDaoInterface {

    use DaoFkNonuniqueTrait;
    use DaoFkUniqueTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['company_id' , 'login_login_name_fk'];
    }

    public function getForeignKeyAttributes(): array {
        return [
            'company_id_fk'=>['company_id'],
            'login_login_name_fk'=>['login_login_name_fk']
            
        ];
    }

    public function getAttributes(): array {
        return [            
            'company_id',
            'login_login_name_fk'
        ];
    }

    public function getTableName(): string {
        return 'representative';
    }

    
    
    public function findByCompanyIdFk(array $companyIdFk): array {
        $this->findByFk('company_id', $companyIdFk);
    }
    
    public function getByLoginNameFk(array $loginNameFk ): array {
        return $this->getByFk('login_login_name_fk', $loginNameFk);
    }
}
