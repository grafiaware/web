<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\PozadovaneVzdelani;

/**
 *
 * @author vlse2610
 */
interface PozadovaneVzdelaniInterface extends PersistableEntityInterface {

    /**
     * 
     * @return type
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
     * @return PozadovaneVzdelaniInterface $this
     */
    public function setStupen( $stupen) : PozadovaneVzdelaniInterface ;
    /**
     * 
     * @param string $vzdelani
     * @return PozadovaneVzdelaniInterface $this
     */
    public function setVzdelani(  $vzdelani) : PozadovaneVzdelaniInterface ;
   
    
}
