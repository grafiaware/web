<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoAutoincrementTrait;
use Model\Dao\DaoReferenceNonuniqueTrait;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class PaperSectionDao extends DaoEditAbstract implements PaperSectionDaoInterface {

    const REFERENCE_PAPER = 'paper';

    use DaoAutoincrementTrait;
    use DaoReferenceNonuniqueTrait;

    public function getAutoincrementFieldName() {
        return 'id';
    }

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

    public function getReferenceAttributes($referenceName): array {
        return [
        self::REFERENCE_PAPER=>['paper_id_fk'=>'id']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'paper_section';
    }

    public function getContextConditions(): array {
        $contextConditions = [];
        if (isset($this->contextFactory) AND $this->contextFactory->showOnlyPublished()) {
            $contextConditions[] = "paper_section.active = 1";
        }
        return $contextConditions;
    }
}
