<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventContentTypeDao extends DaoEditAbstract implements DaoKeyDbVerifiedInterface {

    public function getPrimaryKeyAttribute(): array {
        return ['type'];
    }

    public function getAttributes(): array {
        return ['type', 'name'];
    }

    public function getTableName(): string {
        return 'event_content_type';
    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Object EventContentTypeDao neumožňuje insertovat bez ověření duplicity klíče. Nelze vkládat metodou insert(), je nutné používat insertWithKeyVerification().");
    }
}
