<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Model\Context\ContextFactoryInterface;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoAutoincrementTrait;
use Model\Dao\DaoReadonlyAbstract;

use Model\RowData\RowDataInterface;

/**
 * Description of RsDao
 *
 * @author pes2704
 */
class PaperContentDao extends DaoEditAbstract implements DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
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

    public function getTableName(): string {
        return 'paper_content';
    }

    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['active'] = "paper_content.active = 1";
                    $contextConditions['actual'] = "(ISNULL(paper_content.show_time) OR paper_content.show_time<=CURDATE()) AND (ISNULL(paper_content.hide_time) OR CURDATE()<=paper_content.hide_time)";
                }
            }
        }
        return $contextConditions;
    }
}
