<?php
namespace Events\Model\Entity;

use Pes\Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface EventLinkPhaseInterface extends PersistableEntityInterface {
   
    public function getId() ;    

    public function getText() ;    
    
    public function setId($id) :EventLinkPhaseInterface;
    
    public function setText( $text) :EventLinkPhaseInterface;      
      
}