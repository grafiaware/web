<?php

namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;
use Events\Model\Dao\CompanyContactDaoInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of ComponentContactDao
 *
 * @author vlse2610
 */
class CompanyContactDao extends DaoTableAbstract implements  CompanyContactDaoInterface {
    use DaoAutoincrementTrait;

    private $keyAttribute = 'id';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
//  `company_contact``id`  // NOT NULL AUTO_INCREMENT,
//  `company_id`   NOT NULL,
//  `name`  
//  `phones`
//  `mobiles` 
//  `emails` 
    
    /**
    * Vrací jednu řádku tabulky 'company_contact' ve formě asociativního pole podle primárního klíče.
    *
    * @param string $id Hodnota primárního klíče
    * @return array Asociativní pole
    */
    public function get($id) {
        $select = $this->select("
            `company_contact`.`id`,  
            `company_contact`.`company_id` , 
            `company_contact`.`name`,  
            `company_contact`.`phones`,
            `company_contact`.`mobiles`, 
            `company_contact`.`emails` 
            ");
        $from = $this->from("`events`.`company_contact`");
        $where = $this->where("`company_contact`.`id` = :id");
        $touplesToBind = [':id' => $id];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    
    public function find($whereClause="", $touplesToBind=[]) {
        $select = $this->select("
            `company_contact`.`id`,  
            `company_contact`.`company_id`, 
            `company_contact`.`name`,
            `company_contact`.`phones`,
            `company_contact`.`mobiles`, 
            `company_contact`.`emails` 
            ");
        $from = $this->from("`events`.`company_contact`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    
    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('company_contact', $rowData);
    }

    
    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('company_contact', ['id'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('company_contact', ['id'], $rowData);
    }

    
    
}
