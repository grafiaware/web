<?php
namespace Events\Model\Entity;

use Model\Entity\EntityInterface;
use Events\Model\Entity\PozadovaneVzdelani;

/**
 *
 * @author vlse2610
 */
interface PozadovaneVzdelaniInterface extends EntityInterface {

    /**
     * 
     * @return int
     */    
    public function getStupen()  ;    
    /**
     * 
     * @return string
     */
    public function getVzdelani() ;    
    /**
     * 
     * @param int $stupen
     * @return PozadovaneVzdelaniInterface
     */
    public function setStupen( $stupen) : PozadovaneVzdelaniInterface ;
    /**
     * 
     * @param string $vzdelani
     * @return PozadovaneVzdelaniInterface
     */
    public function setVzdelani(  $vzdelani) : PozadovaneVzdelaniInterface ;
   
    
}
