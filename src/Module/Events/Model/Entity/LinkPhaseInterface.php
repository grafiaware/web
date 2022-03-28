<?php

namespace Events\Model\Entity;

use Model\Entity\EntityInterface;


/**
 *
 * @author vlse2610
 */
interface LinkPhaseInterface  extends EntityInterface {
    
    public function getId(): ?int ;
    
    public function getText(): ?string ;
    
    public function setId($id) :LinkPhaseInterface ;
    
    public function setText(string $value=null)  :LinkPhaseInterface ;
    
    
}
