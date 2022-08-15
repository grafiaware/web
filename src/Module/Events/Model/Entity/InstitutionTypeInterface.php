<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionTypeInterface  extends EntityInterface {
    
    public function getId() ;

    public function getInstitutionType();            
    

    public function setId($id): InstitutionTypeInterface;

    public function setInstitutionType( $value=null): InstitutionTypeInterface;

    }