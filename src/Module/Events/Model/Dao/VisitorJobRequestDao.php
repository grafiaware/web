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
//      CONSTRAINT `document_id_fk_cvpost` FOREIGN KEY (`cv_document`) REFERENCES `document` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
//  CONSTRAINT `document_id_fk_letterpost` FOREIGN KEY (`letter_document`) REFERENCES `document` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
//  CONSTRAINT `fk_visitor_data_post_job1` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
//  CONSTRAINT `fk_visitor_data_post_login1` FOREIGN KEY (`login_login_name`) REFERENCES `login` (`login_name`) ON DELETE NO ACTION ON UPDATE NO ACTION

    public function getForeignKeyAttributes(): array {
        return [
            'job_id'=>['job_id'],
            'login_login_name'=>['login_login_name'],
        ];
    }

    public function getTableName(): string {
        return 'visitor_job_request';
    }

    public function findJobRequestsByJob(array $jobId): array {
        return $this->findByFk( 'job_id', $jobId);
    }

    public function findJobRequestsByLogin(array $loginName): array {
        return $this->findByFk( 'login_login_name', $loginName);
    }

}

