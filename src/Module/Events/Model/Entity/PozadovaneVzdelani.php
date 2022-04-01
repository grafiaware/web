<?php
namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\PozadovaneVzdelaniInterface;

/**
 * Description of PozadovaneVzdelani
 *
 * @author vlse2610
 */
class PozadovaneVzdelani extends EntityAbstract implements PozadovaneVzdelaniInterface {

    private $stupen;  //int NOT NULL
    private $vzdelani;   //NOT NULL
    
    private $keyAttribute = 'stupen';
      
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getStupen() :int {
        return $this->stupen;
    }
    /**
     * 
     * @return string
     */
    public function getVzdelani() :string {
        return $this->vzdelani;
    }
    /**
     * 
     * @param type $stupen
     * @return PozadovaneVzdelaniInterface
     */
    public function setStupen( $stupen) : PozadovaneVzdelaniInterface {
        $this->stupen = $stupen;
        return $this;
    }
    /**
     * 
     * @param string $vzdelani
     * @return PozadovaneVzdelaniInterface
     */
    public function setVzdelani( string $vzdelani) : PozadovaneVzdelaniInterface {
        $this->vzdelani = $vzdelani;
        return $this;
    }


    
}
