<?php


namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of Login
 *
 * @author pes2704
 */
class Login extends EntityAbstract implements LoginInterface {

    /**
     * @var string
     */
    private $loginName;

    /**
     *
     * @return string|null
     */
    public function getLoginName() {
        return $this->loginName;
    }

    /**
     *
     * @param string $loginName
     * @return LoginInterface
     */
    public function setLoginName( $loginName): LoginInterface {
        $this->loginName = $loginName;
        return $this;
    }

}
