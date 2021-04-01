<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Model\Dao\DaoKeyDbVerifiedInterface;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
/**
 * Description of EnrollDao
 *
 * @author pes2704
 */
class VisitorDataPostDao extends DaoAbstract implements DaoKeyDbVerifiedInterface {

    /**
     * Pro tabulky s auto increment id.
     *
     * @return type
     */
    public function getLastInsertedId() {
        return $this->getLastInsertedIdForOneRowInsert();
    }

    public function get($id) {
        $sql = "
SELECT `visitor_data_post`.`login_name`,
    `visitor_data_post`.`short_name`,
    `visitor_data_post`.`prefix`,
    `visitor_data_post`.`name`,
    `visitor_data_post`.`surname`,
    `visitor_data_post`.`postfix`,
    `visitor_data_post`.`email`,
    `visitor_data_post`.`phone`,
    `visitor_data_post`.`cv_education_text`,
    `visitor_data_post`.`cv_skills_text`,
    `visitor_data_post`.`cv_document`,
    `visitor_data_post`.`cv_document_filename`,
    `visitor_data_post`.`cv_document_mimetype`,
    `visitor_data_post`.`letter_document`,
    `visitor_data_post`.`letter_document_filename`,
    `visitor_data_post`.`letter_document_mimetype`
FROM `visitor_data_post`
WHERE
                `visitor_data_post`.`login_name` = :login_name";

        return $this->selectOne($sql, [':login_name' => $id], TRUE);
    }

    public function findAll() {
        $sql = "
SELECT `visitor_data_post`.`login_name`,
    `visitor_data_post`.`short_name`,
    `visitor_data_post`.`prefix`,
    `visitor_data_post`.`name`,
    `visitor_data_post`.`surname`,
    `visitor_data_post`.`postfix`,
    `visitor_data_post`.`email`,
    `visitor_data_post`.`phone`,
    `visitor_data_post`.`cv_education_text`,
    `visitor_data_post`.`cv_skills_text`,
    `visitor_data_post`.`cv_document`,
    `visitor_data_post`.`cv_document_filename`,
    `visitor_data_post`.`cv_document_mimetype`,
    `visitor_data_post`.`letter_document`,
    `visitor_data_post`.`letter_document_filename`,
    `visitor_data_post`.`letter_document_mimetype`
FROM `visitor_data_post`";

        return $this->selectMany($sql, []);
    }

    public function find($whereClause=null, $touplesToBind=[]) {
        $sql = "
SELECT `visitor_data_post`.`login_name`,
    `visitor_data_post`.`short_name`,
    `visitor_data_post`.`prefix`,
    `visitor_data_post`.`name`,
    `visitor_data_post`.`surname`,
    `visitor_data_post`.`postfix`,
    `visitor_data_post`.`email`,
    `visitor_data_post`.`phone`,
    `visitor_data_post`.`cv_education_text`,
    `visitor_data_post`.`cv_skills_text`,
    `visitor_data_post`.`cv_document`,
    `visitor_data_post`.`cv_document_filename`,
    `visitor_data_post`.`cv_document_mimetype`,
    `visitor_data_post`.`letter_document`,
    `visitor_data_post`.`letter_document_filename`,
    `visitor_data_post`.`letter_document_mimetype`
FROM `visitor_data_post`"
            .$this->where($whereClause);
        return $this->selectMany($sql, $touplesToBind);
    }

    private function getWithinTransaction(HandlerInterface $dbhTransact, $loginName, $shortName) {
        if ($dbhTransact->inTransaction()) {
                $stmt = $dbhTransact->prepare(
                        "SELECT `visitor_data_post`.`login_name`,  `visitor_data_post`.`short_name`
                        FROM  `visitor_data_post`
                        WHERE `visitor_data_post`.`login_name` = :login_name AND  `visitor_data_post`.`short_name` = :short_name LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam( ':login_name' , $loginName );
                $stmt->bindParam( ':short_name' , $shortName );
                $o = $stmt->execute();   //false - kdyz chyba,   i kdyz nenejde je to ok vysledek, cili true

                $count = $stmt->rowCount();
            return  $count ? true : false;
        } else {
            throw new \LogicException('Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.');
        }
    }

    public function insertWithKeyVerification($row) {
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $found = $this->getWithinTransaction($dbhTransact,$row['login_name']);
            if  (! $found)   {
                    $sql = "
            INSERT INTO `visitor_data_post`
            (`login_name`,
            `short_name`,
            `prefix`,
            `name`,
            `surname`,
            `postfix`,
            `email`,
            `phone`,
            `cv_education_text`,
            `cv_skills_text`,
            `cv_document`,
            `cv_document_filename`,
            `cv_document_mimetype`,
            `letter_document`,
            `letter_document_filename`,
            `letter_document_mimetype`)
            VALUES
            (:login_name,
            :short_name,
            :prefix,
            :name,
            :surname,
            :postfix,
            :email,
            :phone,
            :cv_education_text,
            :cv_skills_text,
            :cv_document,
            :cv_document_filename,
            :cv_document_mimetype,
            :letter_document,
            :letter_document_filename,
            :letter_document_mimetype)";
                $stmt = $dbhTransact->prepare($sql);
                $stmt->bindParam(':login_name', $row['login_name'] );
                $stmt->bindParam(':short_name', $row['short_name'] );

                $stmt->bindParam(':prefix', $row['prefix']);
                $stmt->bindParam(':name', $row['name']);
                $stmt->bindParam(':surname', $row['surname']);
                $stmt->bindParam(':postfix', $row['postfix']);
                $stmt->bindParam(':email', $row['email']);
                $stmt->bindParam(':phone', $row['phone']);
                $stmt->bindParam(':cv_education_text', $row['cv_education_text']);
                $stmt->bindParam(':cv_skills_text', $row['cv_skills_text']);
                $stmt->bindParam(':cv_document', $row['cv_document']);
                $stmt->bindParam(':cv_document_filename', $row['cv_document_filename']);
                $stmt->bindParam(':cv_document_mimetype', $row['cv_document_mimetype']);
                $stmt->bindParam(':letter_document', $row['letter_document']);
                $stmt->bindParam(':letter_document_filename', $row['letter_document_filename']);
                $stmt->bindParam(':letter_document_mimetype', $row['letter_document_mimetype']);

                $stmt->execute();
            } else {
                throw new DaoKeyVerificationFailedException("Přihlašovací jméno (login name) již existuje.");
            }
            /*** commit the transaction ***/
            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
    }

    public function update($row) {

        $sql = "
UPDATE `visitor_data_post`
SET
`prefix` = :prefix,
`name` = :name,
`surname` = :surname,
`postfix` = :postfix,
`email` = :email,
`phone` = :phone,
`cv_education_text` = :cv_education_text,
`cv_skills_text` = :cv_skills_text,
`cv_document` = :cv_document,
`cv_document_filename` = :cv_document_filename,
`cv_document_mimetype` = :cv_document_mimetype,
`letter_document` = :letter_document,
`letter_document_filename` = :letter_document_filename,
`letter_document_mimetype` = :letter_document_mimetype
WHERE `login_name` = :login_name AND `short_name` = :short_name";

        return $this->execUpdate($sql, [
            ':prefix'=>$row['prefix'],
            ':name'=>$row['name'],
            ':surname'=>$row['surname'],
            ':postfix'=>$row['postfix'],
            ':email'=>$row['email'],
            ':phone'=>$row['phone'],
            ':cv_education_text'=>$row['cv_education_text'],
            ':cv_skills_text'=>$row['cv_skills_text'],
            ':cv_document'=>$row['cv_document'],
            ':cv_document_filename'=>$row['cv_document_filename'],
            ':cv_document_mimetype'=>$row['cv_document_mimetype'],
            ':letter_document'=>$row['letter_document'],
            ':letter_document_filename'=>$row['letter_document_filename'],
            ':letter_document_mimetype'=>$row['letter_document_mimetype'],

            ':login_name'=>$row['login_name'],
            ':short_name'=>$row['short_name']
                ]
            );
    }

    public function delete($row) {
        $sql = "
DELETE FROM `veletrhprace`.`visitor_data_post`
WHERE `login_name` = :login_name AND `short_name` = :short_name";


        return $this->execDelete($sql, [':login_name'=>$row['login_name'], ':short_name'=>$row['short_name']]);
    }

}
