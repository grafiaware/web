<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao\Hierarchy;

use Model\Dao\DaoAbstract;

/**
 * Description of LanguageDao
 *
 * @author pes2704
 */
class HierarchyDao extends DaoAbstract implements HierarchyDaoInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['uid'];
    }

    public function getAttributes(): array {
        return ['uid', 'left_node', 'right_node'];  //bez parent_uid - je ke zrušení
    }

    public function getTableName(): string {
        return 'hierarchy';
    }

}
