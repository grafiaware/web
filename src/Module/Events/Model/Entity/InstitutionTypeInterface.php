<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface InstitutionTypeInterface  extends PersistableEntityInterface {
    
    public function getId() ;

    public function getInstitutionType();            
    

    public function setId($id): InstitutionTypeInterface;

    public function setInstitutionType( $value=null): InstitutionTypeInterface;

    }