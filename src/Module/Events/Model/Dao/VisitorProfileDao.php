<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Model\Dao\DaoTableAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class VisitorProfileDao extends DaoTableAbstract {

    private $keyAttribute = 'login_login_name';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function get($loginName) {
        $select = $this->select("
    `visitor_profile`.`login_login_name`,
    `visitor_profile`.`prefix`,
    `visitor_profile`.`name`,
    `visitor_profile`.`surname`,
    `visitor_profile`.`postfix`,
    `visitor_profile`.`email`,
    `visitor_profile`.`phone`,
    `visitor_profile`.`cv_education_text`,
    `visitor_profile`.`cv_skills_text`,
    `visitor_profile`.`cv_document`,
    `visitor_profile`.`letter_document`
    ");
        $from = $this->from("`visitor_profile`");
        $where = $this->where("`visitor_profile`.`login_login_name` = :login_login_name");
        $touplesToBind = [':login_login_name' => $loginName];
        return $this->selectOne($select, $from, $where, $touplesToBind, TRUE);
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        $select = $this->select("
    `visitor_profile`.`login_login_name`,
    `visitor_profile`.`prefix`,
    `visitor_profile`.`name`,
    `visitor_profile`.`surname`,
    `visitor_profile`.`postfix`,
    `visitor_profile`.`email`,
    `visitor_profile`.`phone`,
    `visitor_profile`.`cv_education_text`,
    `visitor_profile`.`cv_skills_text`,
    `visitor_profile`.`cv_document`,
    `visitor_profile`.`letter_document`
    ");
        $from = $this->from("`visitor_profile`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('visitor_profile', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('visitor_profile', ['login_login_name'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('visitor_profile', ['login_login_name'], $rowData);
    }
}
