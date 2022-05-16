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
    public function getStupen() :int ;    
    /**
     * 
     * @return string
     */
    public function getVzdelani() :string ;    
    /**
     * 
     * @param int $stupen
     * @return PozadovaneVzdelaniInterface
     */
    public function setStupen(int $stupen) : PozadovaneVzdelaniInterface ;
    /**
     * 
     * @param string $vzdelani
     * @return PozadovaneVzdelaniInterface
     */
    public function setVzdelani( string $vzdelani) : PozadovaneVzdelaniInterface ;
   
    
}
