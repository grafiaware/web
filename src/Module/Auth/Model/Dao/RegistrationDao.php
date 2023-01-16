<?php

namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;
use Model\Dao\DaoEditKeyDbVerifiedInterface;
use Model\Dao\DaoReferenceUniqueInterface;
use Model\Dao\DaoReferenceUniqueTrait;
use Model\RowData\RowDataInterface;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of UserDao
 *
 * @author pes2704
 */
class RegistrationDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface, DaoReferenceUniqueInterface {

    const REFERENCE_LOGIN = 'login';

    use DaoReferenceUniqueTrait;

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

    public function getReferenceAttributes($referenceName): array {
        // 'jméno referencované tabulky'=>['cizí klíč potomka (jméno sloupce v potomkovi)'=>'vlastní klíč rodiče (jméno sloupve v rodiči)']
        return [
            'login'=>['login_name_fk'=>'login_name']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'registration';
    }

    public function getByLoginNameFk(array $loginNameFk): array {
        return $this->getByReference('login_name_fk', $loginNameFk);
    }

    /**
     * Insert s generování hodnoty uid.
     *
     * @param RowDataInterface $row
     * @return bool
     * @throws Exception
     */
    public function insert(RowDataInterface $row): bool {
        $dbhTransact = $this->dbHandler;
        try {
            $dbhTransact->beginTransaction();
            $uid = $this->getNewUidWithinTransaction($dbhTransact);
            $row['uid'] = $uid;
            $success = parent::insert($row);
//            $stmt = $dbhTransact->prepare(
//                        "INSERT INTO registration (login_name_fk, password_hash, email, email_time, uid )
//                        VALUES (:login_name_fk, :password_hash, :email, :email_time, :uid  )" );
//            $stmt->bindParam(':login_name_fk', $row['login_name_fk'] );
//            $stmt->bindParam(':password_hash', $row['password_hash'] );
//            $stmt->bindParam(':email', $row['email'] );
//            $stmt->bindParam(':email_time', $row['email_time'] );
//            $stmt->bindParam(':uid', $uid);
//            $stmt->execute();
//                /*** commit the transaction ***/
//            $dbhTransact->commit();
        } catch(Exception $e) {
            $dbhTransact->rollBack();
            throw new Exception($e);
        }
        return $success ?? false;
    }

    /**
     * @param HandlerInterface $dbhTransact
     * @return type
     * @throws \LogicException Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.
     */
    private function getNewUidWithinTransaction(HandlerInterface $dbhTransact) {
        if ($dbhTransact->inTransaction()) {
            do {
                $uid = uniqid();
                $stmt = $dbhTransact->prepare(
                        "SELECT uid
                        FROM registration
                        WHERE uid = :uid LOCK IN SHARE MODE");   //nelze použít LOCK TABLES - to commitne aktuální transakci!
                $stmt->bindParam(':uid', $uid);
                $stmt->execute();
            } while ($stmt->rowCount());
            return $uid;
        } else {
            throw new \LogicException('Tuto metodu lze volat pouze v průběhu spuštěné databázové transakce.');
        }
    }
}
