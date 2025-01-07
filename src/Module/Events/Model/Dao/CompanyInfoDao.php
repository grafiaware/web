<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

/**
 * Description of CompanyAddressDao
 *
 * @author vlse2610
 */
class CompanyInfoDao extends DaoEditAbstract {

    public function getPrimaryKeyAttributes(): array {
        return ['company_id'];  //primarni klic a cizi klic
    }

    public function getAttributes(): array {
        return ['company_id', 'introduction', 'video_link', 'positives', 'social'];
    }

    public function getTableName(): string {
        return 'company_info';
    }

}
