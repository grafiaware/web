<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoFkNonuniqueTrait;
use Events\Model\Dao\JobToTagDaoInterface;


/**
 * Description of JobToTagDao
 *
 * @author vlse2610
 */
class JobToTagDao  extends DaoEditAbstract  implements JobToTagDaoInterface {

    use DaoFkNonuniqueTrait;


    public function getPrimaryKeyAttributes(): array {
        return ['job_id', 'job_tag_tag'];
    }

    public function getForeignKeyAttributes(): array {
        return [
            'job_id'=>['job_id'], //atributy ciziho klice job_id(klic asoc.pole pojmenovan takto)- je jednoslozkovy - (v tabulce sloupec 1 slozky job_id)
            'job_tag_tag'=>['job_tag_tag']
        ];
    }

    public function getAttributes(): array {
        return [
            'job_id',
            'job_tag_tag'
        ];
    }

    public function getTableName(): string {
        return 'job_to_tag';
    }

    public function findByJobIdFk( array $jobIdFk ) {
        return $this->findByFk('job_id', $jobIdFk);
    }

    public function findByJobTagFk( array $jobTagTagFk ) {
        return $this->findByFk('job_tag_tag', $jobTagFk);
    }
}
