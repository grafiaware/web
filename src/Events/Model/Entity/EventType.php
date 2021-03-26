<?php


namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Model\Entity\EntityGeneratedKeyInterface;

/**
 * Description of EventType
 *
 * @author pes2704
 */
class EventType extends EntityAbstract implements EventTypeInterface {

    private $keyAttribute = 'id';

    private $id;
    private $value;

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getId() {
        return $this->id;
    }

    public function getValue(): ?string {
        return $this->value;
    }

    public function setId($id): EventTypeInterface {
        $this->id = $id;
        return $this;
    }

    public function setValue(string $value=null): EventTypeInterface {
        $this->value = $value;
        return $this;
    }

}
