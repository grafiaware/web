<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionTypeInterface  extends EntityInterface {
    
    public function getId(): ?int ;

    public function getInstitutionType(): ?string;            
    

    public function setId($id): InstitutionTypeInterface;

    public function setInstitutionType(string $value=null): InstitutionTypeInterface;

    }