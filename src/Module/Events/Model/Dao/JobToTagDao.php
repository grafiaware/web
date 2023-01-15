<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoReferenceNonuniqueTrait;
use Events\Model\Dao\JobToTagDaoInterface;



/**
 * Description of JobToTagDao
 *
 * @author vlse2610
 */
class JobToTagDao  extends DaoEditAbstract  implements JobToTagDaoInterface {

    use DaoReferenceNonuniqueTrait;


    public function getPrimaryKeyAttributes(): array {
        return ['job_id', 'job_tag_tag'];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
            'job'=>['job_id'=>'id'],
            'job_tag'=>['job_tag_tag'=>'tag']
        ][$referenceName];
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

    public function findByJobIdFk( array $jobIdFk ): array {
        return $this->findByReference('job', $jobIdFk);
    }

    public function findByJobTagFk( array $jobTagTagFk ) : array{
        return $this->findByReference('job_tag', $jobTagTagFk);
    }
}
