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
    
    private $created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_due_to_auth`
    
    //`login_name` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_due_to_auth` tinyint(1) unsigned NOT NULL DEFAULT '0',
    
    
    

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
