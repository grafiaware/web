<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface EventLinkPhaseInterface extends EntityInterface {
   
    public function getId() ;    

    public function getText() ;    
    
    public function setId($id) :EventLinkPhaseInterface;
    
    public function setText( $text=null) :EventLinkPhaseInterface;      
      
}