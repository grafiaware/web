<?php


namespace Auth\Model\Dao;

use Model\Dao\DaoAbstract;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class CredentialsDao extends DaoAbstract {

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

    public function insert($row) {
        $sql = "INSERT INTO credentials (login_name_fk, password_hash, role )
                VALUES (:login_name_fk, :password_hash, :role )";
        return $this->execInsert($sql, [':login_name_fk'=>$row['login_name_fk'],
                                        ':password_hash'=>$row['password_hash'],
                                        ':role'=>$row['role']  ]);
    }

    public function update($row) {
        $sql = "UPDATE credentials SET password_hash = :password_hash, role = :role
                WHERE `login_name_fk` = :login_name_fk";
        return $this->execUpdate($sql, [':password_hash'=>$row['password_hash'],
                                        ':role'=>$row['role'],
                                        ':login_name_fk'=>$row['login_name_fk'] ]);
    }

    public function delete($row) {
        $sql = "DELETE FROM credentials WHERE `login_name_fk` = :login_name_fk";
        return $this->execDelete($sql, [':login_name_fk'=>$row['login_name_fk'] ]);
    }
}
