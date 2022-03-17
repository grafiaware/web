<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of Enroll
 *
 * @author pes2704
 */
class Enroll extends EntityAbstract implements EnrollInterface {

    private $id;
    private $loginName;
    private $eventid;

    private $keyAttribute = 'id';
    
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }


    public function getId(): ?int {
        return $this->id;
    }
    public function getLoginName(): ?string {
        return $this->loginName;
    }
    public function getEventid(): ?string {
        return $this->eventid;
    }

    

    public function setId($id): EnrollInterface {
        $this->id = $id;
        return $this;
    }
    public function setLoginName($loginName = null): EnrollInterface {
        $this->loginName = $loginName;
        return $this;
    }
    public function setEventid($eventid = null): EnrollInterface {
        $this->eventid = $eventid;
        return $this;
    }


}
