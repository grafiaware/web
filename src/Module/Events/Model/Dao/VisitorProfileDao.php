<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

/**
 * Description of VisitorProfileDao
 *
 * @author pes2704
 */
class VisitorProfileDao extends DaoEditAbstract {

    public function getPrimaryKeyAttributes(): array {
        return ['login_login_name'];
    }

    public function getAttributes(): array {
        return [
            'login_login_name',
            'prefix',
            'name',
            'surname',
            'postfix',
            'phone',
            'cv_education_text',
            'cv_skills_text',
            'cv_document',
            'letter_document'
            ];
        }

    public function getTableName(): string {
        return 'visitor_profile';
    }
}
