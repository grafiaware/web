<?php
namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of CompanyAddressDao
 *
 * @author vlse2610
 */
class CompanyAddressDao extends DaoTableAbstract {

    private $keyAttribute = 'company_id';  //primarni klic a cizi klic

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    
    public function get($command_id) {
        $select = $this->select("
            `company_address`.`company_id`,  
            `company_address`.`name`,
            `company_address`.`lokace`,
            `company_address`.`psc`,
            `company_address`.`obec` "  );
        $from = $this->from(" `company_address`");
        $where = $this->where("`company_address`.`company_id` = :company_id");
        $touples = [':company_id' => $command_id];
        return $this->selectOne($select, $from, $where, $touples, true);
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `company_address`.`company_id`,  
            `company_address`.`name`,
            `company_address`.`lokace`,
            `company_address`.`psc`,
            `company_address`.`obec` " );
        $from = $this->from(" `company_address` ");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('company_address', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('company_address', ['company_id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('company_address', ['company_id'], $rowData);
    }
}
