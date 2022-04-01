<?php


namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class CredentialsDao extends DaoEditAbstract {

    private $keyAttribute = 'login_name_fk';

    public function getPrimaryKeyAttribute() {
        return $this->keyAttribute;
    }

    /**
     *
     *
     * @param type $loginName
     */
    public function get($loginNameFK) {
        $select = $this->select("
            `credentials`.`login_name_fk`,
            `credentials`.`password_hash`,
            `credentials`.`role`,
            `credentials`.`created`,
            `credentials`.`updated`
            ");
        $from = $this->from("`credentials`");
        $where = $this->where("`credentials`.`login_name_fk` = :login_name_fk");
        $touplesToBind = [':login_name_fk' => $loginNameFK];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function getByFk($loginNameFk) {
        $select = $this->select("
            `credentials`.`login_name_fk`,
            `credentials`.`password_hash`,
            `credentials`.`role`,
            `credentials`.`created`,
            `credentials`.`updated`
            ");
        $from = $this->from("`credentials`");
        $where = $this->where("`credentials`.`login_name_fk` = :login_name_fk");
        $touplesToBind = [':login_name_fk' => $loginNameFk];
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function find($whereClause = "", $touplesToBind = []) {
        $select = $this->select("
            `credentials`.`login_name_fk`,
            `credentials`.`password_hash`,
            `credentials`.`role`,
            `credentials`.`created`,
            `credentials`.`updated`
            ");
        $from = $this->from("`credentials`");
        $where = $this->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    public function insert(RowDataInterface $rowData) {
        return $this->execInsert('credentials', $rowData);
    }

    public function update(RowDataInterface $rowData) {
        return $this->execUpdate('credentials', ['login_name_fk'], $rowData);
    }

    public function delete(RowDataInterface $rowData) {
        return $this->execDelete('credentials', ['login_name_fk'], $rowData);
    }
}
