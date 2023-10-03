<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Model\Entity\EventContentTypeInterface;


/**
 *
 * @author pes2704
 */
interface EventContentTypeInterface extends PersistableEntityInterface {

     public function getId();  
     
    /**
     *
     * @return string|null
     */
    public function getType();

    /**
     *
     * @return string|null
     */
    public function getName();

    /**
     * 
     * @param type $id
     * @return EventContentTypeInterface
     */
    public function setId($id) : EventContentTypeInterface;
    
    /**
     *
     * @param string $type
     * @return EventContentTypeInterface
     */
    public function setType( $type ): EventContentTypeInterface;

    /**
     *
     * @param string|null $name
     * @return EventContentTypeInterface
     */
    public function setName( $name ): EventContentTypeInterface;

}
