<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610 
 */
interface EventLinkInterface  extends EntityInterface {
    

    public function getId() ;    

    public function getShow() ;    

    public function getHref() ;    

    public function getLinkPhaseIdFk();    

    
    public function setId($id) :EventLinkInterface;
    
    public function setShow($show) :EventLinkInterface;   

    public function setHref( string $href=null) :EventLinkInterface;      
      
    public function setLinkPhaseIdFk($linkPhaseIdFk) :EventLinkInterface;   
}
