<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Events\Model\Dao\VisitorJobRequestDaoInterface;
use Model\Dao\DaoReferenceNonuniqueTrait;



/**
 * Description of VisitorJobRequestDao
 *
 * @author pes2704
 */
class VisitorJobRequestDao extends DaoEditAbstract implements VisitorJobRequestDaoInterface {
    use DaoReferenceNonuniqueTrait;


    public function getPrimaryKeyAttributes(): array {
        return ['login_login_name', 'job_id'] ;
    }

    public function getAttributes(): array {
        return [
            'login_login_name`',
            'job_id',
            'position_name',
            'prefix',
            'name',
            'surname',
            'postfix',
            'email',
            'phone',
            'cv_education_text',
            'cv_skills_text',
            'cv_document',
            'letter_document'
        ];
    }

    public function getReferenceAttributes($referenceName): array {
        return [
            'job_id'=>['job_id'],
            'login_login_name'=>['login_login_name'],
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'visitor_job_request';
    }

    public function findJobRequestsByJob(array $jobId): array {
        return $this->findByReference( 'job_id', $jobId);
    }

    public function findJobRequestsByLogin(array $loginName): array {
        return $this->findByReference( 'login_login_name', $loginName);
    }

}

