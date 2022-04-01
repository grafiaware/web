<?php
namespace Events\Model\Dao;

use Model\Dao\DaoAutoincrementKeyInterface;
use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoAutoincrementTrait;
use Model\RowData\RowDataInterface;

/**
 * Description of CompanyDao
 *
 * @author vlse2610
 */
class CompanyDao  extends DaoEditAbstract implements  DaoAutoincrementKeyInterface {

    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getNonPrimaryKeyAttribute(): array {
        return ['id', 'name', 'eventInstitutionName30'];
    }

    public function getTableName(): string {
        return "company";
    }

    /**
     * Vrací jednu řádku tabulky 'company' ve formě asociativního pole podle primárního klíče.
     *
     * @param string $id Hodnota primárního klíče
     * @return array Asociativní pole
     */
//    public function get($id) {
//        $select = $this->select("
//            `company`.`id`,
//            `company`.`name`,
//            `company`.`eventInstitutionName30`
//            ");
//        $from = $this->from("`events`.`company`");
//        $where = $this->where("`company`.`id` = :id");
//        $touplesToBind = [':id' => $id];
//        return $this->selectOne($select, $from, $where, $touplesToBind, true);
//    }
    public function get($id) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->sql->touples($this->getPrimaryKeyAttribute())));
        $touplesToBind = $this->getPrimaryKeyTouplesToBind($id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }
}
