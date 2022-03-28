<?php

namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;

use \Model\Dao\DaoAutoincrementTrait;
use Model\RowData\RowDataInterface;

use Events\Model\Dao\EventLinkPhaseDaoInterface;


/**
 * Description of EventLinkPhaseDao
 *
 * @author vlse2610
 */
class EventLinkPhaseDao extends DaoTableAbstract implements EventLinkPhaseDaoInterface {

    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     * Vrací jednu řádku tabulky 'event_link_phase' ve formě asociativního pole podle primárního klíče.
     *
     * @param int $id Hodnota primárního klíče
     * @return array Asociativní pole
     */
    public function get( $id) {

        $select = $this->select("
            `event_link_phase`.`id`,
            `event_link_phase`.`text`
            ");
        $from = $this->from("`event_link_phase`");
        $where = $this->where("`event_link_phase`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getOutOfContext(...$id) {
        ;
    }



    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `event_link_phase`.`id`,
            `event_link_phase`.`text`
            ");
        $from = $this->from("`event_link_phase`");
        $where = $this->where($this->and($this->getContextConditions(), $whereClause));
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('event_link_phase', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('event_link_phase', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('event_link_phase', ['id'], $rowData);
    }



}
