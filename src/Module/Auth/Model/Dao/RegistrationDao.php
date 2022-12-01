<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\RowData\RowDataInterface;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class RegistrationDao extends DaoEditAbstract {


    public function getPrimaryKeyAttributes(): array {
        return ['login_name_fk'];
    }

    public function getAttributes(): array {
        return [
            'login_name_fk',
            'password_hash',
            'email',
            'email_time',
            'uid',
            'info',
            'created'
        ];
    }

    public function getTableName(): string {
        return 'registration';
    }


####################################

    public function insert(RowDataInterface $rowData): bool {
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $uid = $this->getNewUidWithinTransaction($dbhTransact);
            $rowData['uid'] = $uid;
            parent::execInsert($rowData);
                /*** commit the transaction ***/
            $success = $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $success ?? false;
    }

    /**
     * @param HandlerInterface $dbhTransact
     * @param string $uidColumnName
     * @return type
     * @throws \LogicException Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.
     */
    private function getNewUidWithinTransaction(HandlerInterface $dbhTransact, $uidColumnName='uid') {
        if ($dbhTransact->inTransaction()) {
            do {
                $uid = uniqid();
                $stmt = $dbhTransact->prepare(
                        "SELECT $uidColumnName
                        FROM {$this->getTableName()}
                        WHERE $uidColumnName = :$uidColumnName LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam(":$uidColumnName", $uid);
                $stmt->execute();
            } while ($stmt->rowCount());
            return $uid;
        } else {
            throw new \LogicException('Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.');
        }

    }
}
