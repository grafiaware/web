<?php
namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;
use Model\Dao\DaoAutoincrementTrait;

use Events\Model\Dao\InstitutionTypeDaoInterface;


/**
 * Description of InstitutionTypeDao
 *
 * @author vlse2610
 */
class InstitutionTypeDao extends DaoTableAbstract implements InstitutionTypeDaoInterface {

    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     * Vrací jednu řádku tabulky 'institution_type' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     * @throws StatementFailureException
     */
    public function get($id) {
        $select = $this->select("
            `institution_type`.`id`,
            `institution_type`.`institution_type`
            ");
        $from = $this->from("`events`.`institution_type`");
        $where = $this->where("`institution_type`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `institution_type`.`id`,
            `institution_type`.`institution_type`
            ");
        $from = $this->from("`events`.`institution_type`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('institution_type', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('institution_type', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('institution_type', ['id'], $rowData);
    }

}