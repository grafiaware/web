<?php


namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Model\Entity\EntityGeneratedKeyInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class EventContentType extends EntityAbstract implements EventContentTypeInterface {

    private $keyAttribute = 'type';

    private $type;

    /**
     * @var string
     */
    private $name;

    
    
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getType(): ?string {
        return $this->type;
    }

    /**
     *
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     *
     * @param string $type
     * @return EventContentTypeInterface
     */
    public function setType(string $type): EventContentTypeInterface {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @param string $name
     * @return EventContentTypeInterface
     */
    public function setName(string $name): EventContentTypeInterface {
        $this->name = $name;
        return $this;
    }

}
