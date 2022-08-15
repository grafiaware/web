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

    private $type;

    /**
     * @var string
     */
    private $name;

    public function getType() {
        return $this->type;
    }

    /**
     *
     * @return string|null
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * @param string $type
     * @return EventContentTypeInterface
     */
    public function setType( $type): EventContentTypeInterface {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @param string $name
     * @return EventContentTypeInterface
     */
    public function setName( $name): EventContentTypeInterface {
        $this->name = $name;
        return $this;
    }

}
