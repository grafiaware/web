<?php
namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\EventLinkPhaseInterface;

/**
 * Description of EventLinkPhase
 *
 * @author vlse2610
 */
class EventLinkPhase extends EntityAbstract implements EventLinkPhaseInterface {
    
    private $id;
    private $text;
        
    private $keyAttribute = 'id';
        
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    
    
    public function getId() : int {
        return $this->id;
    }
    public function getText() : ?string {
        return $this->text;
    }   
    
    public function setId($id) :EventLinkPhaseInterface {
        $this->id = $id;
        return $this;
    }
    
    public function setText( string $text=null) :EventLinkPhaseInterface {
        $this->text = $text;
        return $this;
    }      
      
}
