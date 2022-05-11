<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * Description of LanguageDao
 *
 * @author pes2704
 */
class LanguageDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['lang_code'];
    }

    public function getAttributes(): array {
        return ['lang_code', 'locale', 'name', 'collation', 'state'];
    }

    public function getTableName(): string {
        return 'language';
    }

}
