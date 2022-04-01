<?php

namespace Events\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;
use Events\Model\Dao\CompanyContactDaoInterface;
use Model\Dao\DaoAutoincrementTrait;


/**
 * Description of ComponentContactDao
 *
 * @author vlse2610
 */
class CompanyContactDao extends DaoEditAbstract implements  CompanyContactDaoInterface {
    use DaoAutoincrementTrait;

    public function getPrimaryKeyAttribute(): array {
        return ['id'];
    }

    public function getNonPrimaryKeyAttribute(): array {
        return [
            'id',
            'company_id',
            'name',
            'phones',
            'mobiles',
            'emails'
        ];
    }

    public function getTableName(): string {
        return 'company_contact';
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
    public function get(...$id) {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->touples($this->getPrimaryKeyAttribute()));
        $touplesToBind = $this->getPrimaryKeyTouplesToBind(...$id);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }


}
