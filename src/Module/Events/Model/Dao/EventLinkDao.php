<?php
namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoAutoincrementTrait;
use Model\RowData\RowDataInterface;

use Events\Model\Dao\EventLinkDaoInterface;

/**
 * Description of EventLikDao
 *
 * @author vlse2610
 */
class EventLinkDao  extends DaoEditAbstract implements EventLinkDaoInterface {

    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getPrimaryKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     * Vrací jednu řádku tabulky 'event_link' ve formě asociativního pole podle primárního klíče.
     *
     * @param int $id Hodnota primárního klíče
     * @return array Asociativní pole
     */
    public function get( $id) {

        $select = $this->select("
            `event_link`.`id`,
            `event_link`.`show` ,
            `event_link`.`href`,
            `event_link`.`link_phase_id_fk`
            ");
        $from = $this->from("`event_link`");
        $where = $this->where("`event_link`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

//    public function getOutOfContext(...$id) {
//        ;
//    }

    public function find($whereClause="", $touplesToBind=[]): iterable{
        $select = $this->select("
            `event_link`.`id`,
            `event_link`.`show` ,
            `event_link`.`href`,
            `event_link`.`link_phase_id_fk`
            ");
        $from = $this->from("`event_link` ");
        $where = $this->where( $whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('event_link', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('event_link', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('event_link', ['id'], $rowData);
    }



}
