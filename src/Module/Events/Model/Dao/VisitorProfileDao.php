<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class VisitorProfileDao extends DaoEditAbstract {

    public function getPrimaryKeyAttribute(): array {
        return 'login_login_name';
    }

    public function getAttributes(): array {
        return [
            'login_login_name',
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

    public function getTableName(): string {
        return 'visitor_profile';
    }
}
