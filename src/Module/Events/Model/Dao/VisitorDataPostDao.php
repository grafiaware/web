<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Pes\Database\Handler\HandlerInterface;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\Exception\DaoKeyVerificationFailedException;

use Model\RowData\RowDataInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class VisitorDataPostDao extends DaoEditAbstract implements DaoKeyDbVerifiedInterface {

    public function getPrimaryKeyAttribute(): array {
        return 'login_name';
    }

    public function getAttributes(): array {
        return [
            'login_name`',
            'short_name',
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
            'cv_document_filename',
            'cv_document_mimetype',
            'letter_document',
            'letter_document_filename',
            'letter_document_mimetype'
        ];
    }

    public function getTableName(): string {
        return 'visitor_data_post';
    }
}
