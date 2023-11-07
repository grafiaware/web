<?php


namespace Auth\Model\Dao;

use Model\Dao\DaoEditAbstract;

use Model\Dao\DaoReferenceUniqueInterface;
use Model\Dao\DaoReferenceUniqueTrait;
/**
 * Description of UserDao
 *
 * @author pes2704
 */
class CredentialsDao extends DaoEditAbstract implements DaoReferenceUniqueInterface {

    const REFERENCE_LOGIN = 'login';

    use DaoReferenceUniqueTrait;

    public function getPrimaryKeyAttributes(): array {
        return [
            'login_name_fk'
        ];
    }

    public function getAttributes(): array {
        return [
            'login_name_fk',
            'password_hash',
            'role_fk',
            'created',
            'updated'
        ];
    }
    public function getReferenceAttributes($referenceName): array {
        // 'jméno referencované tabulky'=>['cizí klíč potomka (jméno sloupce v potomkovi)'=>'vlastní klíč rodiče (jméno sloupce v rodiči)']
        return [
            self::REFERENCE_LOGIN=>['login_name_fk'=>'login_name']
        ][$referenceName];
    }

    public function getTableName(): string {
        return 'credentials';
    }

}
