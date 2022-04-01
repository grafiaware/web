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

    private $keyAttribute = 'type';

    public function getPrimaryKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($type) {
        $select = $this->select("`event_content_type`.`type`,
                `event_content_type`.`name`");
        $from = $this->from("`event_content_type`");
        $where = $this->where("`event_content_type`.`type` = :type");
        $touplesToBind = [':type' => $type];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]): iterable{
        $select = $this->select("`event_content_type`.`type`,
                `event_content_type`.`name`");
        $from = $this->from("`event_content_type`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insertWithKeyVerification(RowDataInterface $rowData) {
        $this->execInsertWithKeyVerification('event_content_type', ['type'], $rowData);
    }

    public function insert(RowDataInterface $rowData) {
        throw new DaoForbiddenOperationException("Object EventContentTypeDao neumožňuje insertovat bez ověření duplicity klíče. Nelze vkládat metodou insert(), je nutné používat insertWithKeyVerification().");
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('event_content_type', ['type'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('event_content_type', ['type'], $rowData);
    }
}
