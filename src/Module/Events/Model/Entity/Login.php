<?php


namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\LoginInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class Login extends PersistableEntityAbstract implements LoginInterface {

    /**
     * @var string
     */
    private $loginName; //NOT NULL

    /**
     *
     * @return string
     */
    public function getLoginName() {
        return $this->loginName;
    }

    /**
     *
     * @param string $loginName
     * @return LoginInterface  $this
     */
    public function setLoginName( $loginName): LoginInterface {
        $this->loginName = $loginName;
        return $this;
    }

}
