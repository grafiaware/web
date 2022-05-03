<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditInterface;

/**
 * Description of JobToTagDao
 *
 * @author vlse2610
 */
class PozadovaneVzdalaniDao  extends DaoEditAbstract  implements DaoEditInterface{

    public function getPrimaryKeyAttribute(): array {
        return ['stupen'];
    }

//    public function getForeignKeyAttributes(): array {
//        return [
//            'job_id'=>['job_id'], //atributy ciziho klice job_id(klic asoc.pole pojmenovan takto)- je jednoslozkovy - (v tabulce sloupec 1 slozky job_id)
//            'job_tag_id'=>['job_tag_id']
//        ];
//    }

    public function getAttributes(): array {
        return [
            'stupen',
            'vzdelani'
        ];
    }

    public function getTableName(): string {
        return 'pozadovane_vzdelani';
    }
}