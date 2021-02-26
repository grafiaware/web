<?php


namespace Model\Entity;

/**
 * Description of Login
 *
 * @author pes2704
 */
class Login extends EntityAbstract implements LoginInterface {

    private $keyAttribute = 'login_name';

    /**
     * @var string
     */
    private $loginName;

    public function getKeyAttribute() {
        return $this->keyAttribute;
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
     * @return \Model\Entity\LoginInterface
     */
    public function setLoginName(string $loginName): LoginInterface {
        $this->loginName = $loginName;
        return $this;
    }

}
