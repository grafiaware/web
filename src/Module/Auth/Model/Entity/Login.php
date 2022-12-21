<?php


namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Auth\Model\Entity\EntityGeneratedKeyInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class Login extends PersistableEntityAbstract implements LoginInterface {

    /**
     * @var string
     */
    private $loginName;

    /**
     * Metoda vrací hash logiName pro účely generování tokenu uživatele. Neukládá v sobě token (není pak součástí serializovaných dat).
     */
    public function getLoginNameHash() {
        return hash('ripemd160', $this->loginName);
    }


    /**
     *
     * @return string|null
     */
    public function getLoginName(): ?string {
        return $this->loginName;
    }

    /**
     *
     * @param string $loginName
     * @return \Auth\Model\Entity\LoginInterface
     */
    public function setLoginName(string $loginName): LoginInterface {
        $this->loginName = $loginName;
        return $this;
    }

}
