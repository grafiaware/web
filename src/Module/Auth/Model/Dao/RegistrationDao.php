<?php

namespace Auth\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;
use Pes\Model\Dao\DaoReferenceUniqueInterface;
use Pes\Model\Dao\DaoReferenceUniqueTrait;
use Pes\Model\RowData\RowDataInterface;

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
     * Loginy registrací s daným e-mailem (porovnání bez ohledu na velikost písmen – kolace sloupce).
     *
     * @param string $email
     * @param bool $confirmedOnly true = jen potvrzené účty (mají credentials)
     * @return string[] login_name_fk
     */
    public function findLoginNamesByEmail(string $email, bool $confirmedOnly): array {
        $sql = "SELECT r.login_name_fk FROM registration r"
             . ($confirmedOnly ? " INNER JOIN credentials c ON c.login_name_fk = r.login_name_fk" : "")
             . " WHERE r.email = :email ORDER BY r.login_name_fk";
        $stmt = $this->getPreparedStatement($sql);
        $stmt->bindValue(':email', trim($email));
        $stmt->execute();
        $names = [];
        while (($name = $stmt->fetchColumn()) !== false) {
            $names[] = $name;
        }
        return $names;
    }

    /**
     * A3: zapíše čas potvrzení registrace. Sloupec confirmed přidává migrace auth 20261106000002 a není v getAttributes(),
     * aby kód fungoval i na DB bez migrace (ostatní sites) – tam se nic nestane.
     */
    public function markConfirmed(string $loginName): void {
        try {
            $stmt = $this->getPreparedStatement(
                "UPDATE registration SET confirmed = NOW() WHERE login_name_fk = :login_name_fk AND confirmed IS NULL");
            $stmt->bindValue(':login_name_fk', $loginName);
            $stmt->execute();
        } catch (\Throwable $e) {
            // DB bez sloupce confirmed (migrace A3 neproběhla) – potvrzení se pozná podle credentials jako dřív
        }
    }

    /**
     * A3: nepotvrzené registrace – bez credentials, confirmed IS NULL. Pro výpis v administraci a úklid.
     *
     * @return array<int, array{login_name_fk: string, email: ?string, info: ?string, created: string}> od nejstarší
     */
    public function findUnconfirmed(?int $olderThanDays = null, ?string $loginPrefix = null): array {
        $sql = "SELECT r.login_name_fk, r.email, r.info, r.created FROM registration r
                WHERE r.confirmed IS NULL
                  AND NOT EXISTS (SELECT 1 FROM credentials c WHERE c.login_name_fk = r.login_name_fk)"
             . ($olderThanDays !== null ? " AND r.created < (NOW() - INTERVAL :days DAY)" : "")
             . ($loginPrefix !== null ? " AND r.login_name_fk LIKE :prefix" : "")
             . " ORDER BY r.created, r.login_name_fk";
        $stmt = $this->getPreparedStatement($sql);
        if ($olderThanDays !== null) {
            $stmt->bindValue(':days', $olderThanDays, \PDO::PARAM_INT);
        }
        if ($loginPrefix !== null) {
            $stmt->bindValue(':prefix', addcslashes($loginPrefix, '%_\\') . '%');
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * A3: smaže nepotvrzené registrace starší než N dní i s loginem (login_token a login_profile kaskádou),
     * v jedné transakci. Účty s credentials se nesmažou nikdy. Vrací smazané loginy.
     * $loginPrefix omezí mazání na loginy s daným začátkem (integrační testy, aby nesmazaly data vývojové DB).
     *
     * @return string[]
     */
    public function deleteUnconfirmedOlderThan(int $days, ?string $loginPrefix = null): array {
        $dbh = $this->dbHandler;
        $dbh->beginTransaction();
        try {
            $select = $dbh->prepare(
                "SELECT r.login_name_fk FROM registration r
                 WHERE r.confirmed IS NULL AND r.created < (NOW() - INTERVAL :days DAY)
                   AND NOT EXISTS (SELECT 1 FROM credentials c WHERE c.login_name_fk = r.login_name_fk)"
                . ($loginPrefix !== null ? " AND r.login_name_fk LIKE :prefix" : "")
                . " FOR UPDATE");
            $select->bindValue(':days', $days, \PDO::PARAM_INT);
            if ($loginPrefix !== null) {
                $select->bindValue(':prefix', addcslashes($loginPrefix, '%_\\') . '%');
            }
            $select->execute();
            $logins = $select->fetchAll(\PDO::FETCH_COLUMN);
            $deleteRegistration = $dbh->prepare("DELETE FROM registration WHERE login_name_fk = :l AND confirmed IS NULL");
            $deleteLogin = $dbh->prepare(
                "DELETE FROM login WHERE login_name = :l
                   AND NOT EXISTS (SELECT 1 FROM credentials c WHERE c.login_name_fk = :l2)
                   AND NOT EXISTS (SELECT 1 FROM registration r WHERE r.login_name_fk = :l3)");
            foreach ($logins as $login) {
                $deleteRegistration->bindValue(':l', $login);
                $deleteRegistration->execute();
                $deleteLogin->bindValue(':l', $login);
                $deleteLogin->bindValue(':l2', $login);
                $deleteLogin->bindValue(':l3', $login);
                $deleteLogin->execute();
            }
            $dbh->commit();
            return $logins;
        } catch (\Throwable $e) {
            $dbh->rollBack();
            throw $e;
        }
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
