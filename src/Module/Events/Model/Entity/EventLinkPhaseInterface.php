<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface EventLinkPhaseInterface extends EntityInterface {
   
    public function getId() : int;    

    public function getText() : ?string;    
    
    public function setId($id) :EventLinkPhaseInterface;
    
    public function setText( string $text=null) :EventLinkPhaseInterface;      
      
}