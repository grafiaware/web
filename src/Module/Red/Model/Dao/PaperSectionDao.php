<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditContextualAbstract;
use Model\Dao\DaoEditAutoincrementKeyInterface;
use Model\Dao\DaoFkNonuniqueInterface;
use Model\Dao\DaoAutoincrementTrait;
use Model\Dao\DaoFkNonuniqueTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class PaperSectionDao extends DaoEditContextualAbstract implements DaoEditAutoincrementKeyInterface, DaoFkNonuniqueInterface {

    use DaoAutoincrementTrait;
    use DaoFkNonuniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return ['id'];
    }

    public function getAttributes(): array {
        return [
            'id',
            'paper_id_fk',
            'content',
            'template_name',
            'template',
            'active',
            'priority',
            'show_time',
            'hide_time',
            'event_start_time',
            'event_end_time',
            'editor',
            'updated',
        ];
    }
    public function getForeignKeyAttributes(): array {
        return [
            'paper_id_fk'=>['paper_id_fk']
        ];
    }
    public function getTableName(): string {
        return 'paper_section';
    }

    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['active'] = "paper_section.active = 1";
                    $contextConditions['actual'] = "(ISNULL(paper_section.show_time) OR paper_section.show_time<=CURDATE()) AND (ISNULL(paper_section.hide_time) OR CURDATE()<=paper_section.hide_time)";
                }
            }
        }
        return $contextConditions;
    }
}
