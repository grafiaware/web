<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610 
 */
interface EventLinkInterface  extends PersistableEntityInterface {
    

    public function getId() ;    

    public function getShow() ;    

    public function getHref() ;    

    public function getLinkPhaseIdFk();    

    
    public function setId($id) :EventLinkInterface;
    
    public function setShow($show) :EventLinkInterface;   

    public function setHref($href) :EventLinkInterface;      
      
    public function setLinkPhaseIdFk($linkPhaseIdFk) :EventLinkInterface;   
}
