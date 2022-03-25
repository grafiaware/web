<?php
namespace Events\Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoTableAbstract;


/**
 * Description of CompanyDao
 *
 * @author vlse2610
 */
class CompanyDao  extends DaoTableAbstract implements  DaoAutoincrementKeyInterface {

     use LastInsertIdTrait;
     
     
       /**
     * Vrací jednu řádku tabulky 'company' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     */     
    public function get($id) {
        $select = $this->select("
            `company`.`id`,
            `company`.`name`,
            `company`.`eventInstitutionName30`
            ");
        $from = $this->from("`events`.`company`");
        $where = $this->where("`company`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `company`.`id`,
            `company`.`name`,
            `company`.`eventInstitutionName30`
            ");
        $from = $this->from("`events`.`company`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('company', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('company', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('company', ['id'], $rowData);
    }
    
}
