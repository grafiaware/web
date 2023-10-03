<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\EventContentTypeInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class EventContentType extends PersistableEntityAbstract implements EventContentTypeInterface {

    private $id;        //NOT NULL
    
    /**
     * 
     * @var string
     */
    private $type;      //NOT NULL
    
    /**
     * @var string
     */
    private $name;

    
    public function getId()  {
        return $this->id;
    }       
    
    /**
     * 
     * @return string
     */
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
     * @param type $id
     * @return EventContentTypeInterface $this
     */
    public function setId($id) : EventContentTypeInterface{
        $this->id = $id;
        return $this;
    }    
    
    /**
     *
     * @param string $type
     * @return EventContentTypeInterface $this
     */
    public function setType( $type): EventContentTypeInterface {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @param string|null $name
     * @return EventContentTypeInterface $this
     */
    public function setName( $name ): EventContentTypeInterface {
        $this->name = $name;
        return $this;
    }

}
