<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Dao;

use Pes\Database\Handler\HandlerInterface;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;
/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class VisitorDataDao extends DaoEditAbstract implements DaoKeyDbVerifiedInterface {

    /**
     * Pro tabulky s auto increment id.
     *
     * @return type
     */
    public function getLastInsertedId() {
        return $this->getLastInsertedId();
    }

    public function get($loginName) {
        $select = $this->select("
    `visitor_data`.`login_name`,
    `visitor_data`.`prefix`,
    `visitor_data`.`name`,
    `visitor_data`.`surname`,
    `visitor_data`.`postfix`,
    `visitor_data`.`email`,
    `visitor_data`.`phone`,
    `visitor_data`.`cv_education_text`,
    `visitor_data`.`cv_skills_text`,
    `visitor_data`.`cv_document`,
    `visitor_data`.`cv_document_filename`,
    `visitor_data`.`cv_document_mimetype`,
    `visitor_data`.`letter_document`,
    `visitor_data`.`letter_document_filename`,
    `visitor_data`.`letter_document_mimetype`
    ");
        $from = $this->from("`visitor_data`");
        $where = $this->where("`visitor_data`.`login_name` = :login_name");
        $touplesToBind = [':login_name' => $loginName];
        return $this->selectOne($select, $from, $where, $touplesToBind, TRUE);
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        $select = $this->select("
    `visitor_data`.`login_name`,
    `visitor_data`.`prefix`,
    `visitor_data`.`name`,
    `visitor_data`.`surname`,
    `visitor_data`.`postfix`,
    `visitor_data`.`email`,
    `visitor_data`.`phone`,
    `visitor_data`.`cv_education_text`,
    `visitor_data`.`cv_skills_text`,
    `visitor_data`.`cv_document`,
    `visitor_data`.`cv_document_filename`,
    `visitor_data`.`cv_document_mimetype`,
    `visitor_data`.`letter_document`,
    `visitor_data`.`letter_document_filename`,
    `visitor_data`.`letter_document_mimetype`
    ");
        $from = $this->from("`visitor_data`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('visitor_data', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('visitor_data', ['login_name'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('visitor_data', ['login_name'], $rowData);
    }
}
