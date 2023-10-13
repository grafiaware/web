<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\PozadovaneVzdelaniInterface;

/**
 * Description of PozadovaneVzdelani
 *
 * @author vlse2610
 */
class PozadovaneVzdelani extends PersistableEntityAbstract implements PozadovaneVzdelaniInterface {

    private $stupen;    //NOT NULL
    private $vzdelani;  //NOT NULL
    
    /**
     *
     * @return type
     */
    public function getStupen()  {
        return $this->stupen;
    }
    /**
     *
     * @return string
     */
    public function getVzdelani() {
        return $this->vzdelani;
    }
    /**
     *
     * @param type $stupen
     * @return PozadovaneVzdelaniInterface $this
     */
    public function setStupen( $stupen) : PozadovaneVzdelaniInterface {
        $this->stupen = $stupen;
        return $this;
    }
    /**
     *
     * @param string $vzdelani
     * @return PozadovaneVzdelaniInterface $this
     */
    public function setVzdelani(  $vzdelani) : PozadovaneVzdelaniInterface {
        $this->vzdelani = $vzdelani;
        return $this;
    }



}
