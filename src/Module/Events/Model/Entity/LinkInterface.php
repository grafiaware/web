<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610 
 */
interface LinkInterface  extends EntityInterface {
    

    public function getId() : ?int  ;
    

    public function getShow() : ?int  ;
    

    public function getHref() : ?string ;
    

    public function getLinkPhaseIdFk(): ?int  ;
    

    public function setId($id) :LinkInterface;
    

    public function setShow($show) :LinkInterface;
    

    public function setHref( string $href=null) :LinkInterface;      
    
    
    public function setLinkPhaseIdFk($linkPhaseIdFk) :LinkInterface;
    

    
}
