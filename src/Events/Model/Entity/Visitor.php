<?php


namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Model\Entity\EntityGeneratedKeyInterface;

/**
 * Description of EventType
 *
 * @author pes2704
 */
class Visitor extends EntityAbstract implements VisitorInterface {

    private $keyAttribute = 'id';

    private $id;
    private $loginName;

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getId() {
        return $this->id;
    }

    public function getLoginName() {
        return $this->loginName;
    }

    public function setId($id): VisitorInterface {
        $this->id = $id;
        return $this;
    }

    public function setLoginName($loginName): VisitorInterface {
        $this->loginName = $loginName;
        return $this;
    }

}
