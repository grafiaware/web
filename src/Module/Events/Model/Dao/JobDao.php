<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of JobDao
 *
 * @author vlse2610
 */
class JobDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'company_id',
            'pozadovane_vzdelani_stupen',
            'nazev',
            'misto_vykonu',
            'popis_pozice',
            'pozadujeme',
            'nabizime'
        ];
    }

     public function getForeignKeyAttributes(): array {
        return [
            `pozadovane_vzdelani_stupen` => ['pozadovane_vzdelani_stupen'] 
           
        ];
    }
    
    public function getTableName(): string {
        return 'job';
    }
}
