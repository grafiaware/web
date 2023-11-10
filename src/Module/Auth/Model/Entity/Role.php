<?php

namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Auth\Model\Entity\RoleInterface;


/**
 * Description of Role
 *
 * @author vlse2610
 */
class Role  extends PersistableEntityAbstract implements RoleInterface { 

    /**
     * @var string
     */
    private $role;   //NOT NULL

    

    /**
     *
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }

    /**
     *
     * @param string $role
     * @return \Auth\Model\Entity\RoleInterface
     */
    public function setRole(string $role): RoleInterface {
        $this->role = $role;
        return $this;
    }


}
