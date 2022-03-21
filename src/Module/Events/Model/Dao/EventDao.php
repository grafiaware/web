<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoContextualAbstract;
use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\LastInsertIdTrait;
use Model\RowData\RowDataInterface;
/**
 * Description of LoginDao
 *
 * @author pes2704
 */
class EventDao extends DaoContextualAbstract implements DaoAutoincrementKeyInterface {

    use LastInsertIdTrait;


    protected function getContextConditions() {
        $contextConditions = [];
        if (isset($this->contextFactory)) {
            $publishedContext = $this->contextFactory->createPublishedContext();
            if ($publishedContext) {
                if ($publishedContext->selectPublished()) {
                    $contextConditions['published'] = "event.published = 1";
                }
            }
        }

        return $contextConditions;
    }

    /**
     * Vrací jednu řádku tabulky 'event' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("
            `event`.`id`,
            `event`.`published`,
            `event`.`start`,
            `event`.`end`,
            `event`.`enroll_link_id_fk`,
            `event`.`enter_link_id_fk`,            
            `event`.`event_content_id_fk`
            ");
        $from = $this->from("`event`");
        $where = $this->where("`event`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
    public function getOutOfContext(...$id) {
        ;
    }
//    /**
//     * Vrací jednu řádku tabulky 'paper' ve formě asociativního pole podle cizího klíče s vazbou 1:1.
//     *
//     * @param string $menuItemIdFk Hodnota cizího klíče
//     * @return array Asociativní pole
//     * @throws StatementFailureException
//     */
//    public function getByTypeFk($eventTypeFk) {
//        $select = $this->select("
//            `event`.`id`,
//            `event`.`published`,
//            `event`.`start`,
//            `event`.`end`,
//            `event`.`enroll_link_id_fk`,
//            `event`.`enter_link_id_fk`,           
//            `event`.`event_content_id_fk`
//            ");
//        $from = $this->from("`event`");
//        $where = $this->where($this->and($this->getContextConditions(), ["`paper`.`event_type_id_fk` = :event_type_id_fk"]));
//        $touplesToBind = [':event_type_id_fk' => $eventTypeFk];
//        return $this->selectOne($select, $from, $where, $touplesToBind, true);
//    }

    
   
    
    
    
    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `event`.`id`,
            `event`.`published`,
            `event`.`start`,
            `event`.`end`,
            `event`.`enroll_link_id_fk`,
            `event`.`enter_link_id_fk`,           
            `event`.`event_content_id_fk`
            ");
        $from = $this->from("`event`");
        $where = $this->where($this->and($this->getContextConditions(), $whereClause));
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('event', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('event', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('event', ['id'], $rowData);
    }
}
