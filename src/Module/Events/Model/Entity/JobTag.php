<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\JobTagInterface;


/**
 * Description of JobTag
 *
 * @author vlse2610
 */
class JobTag  extends PersistableEntityAbstract implements JobTagInterface {
    
    private $id;     //NOT NULL
    private $tag;    //NOT NULL
    private $color;    //NOT NULL
    
    /**
     * 
     * @return string
     */
    public function getId()  {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getTag()  {
        return $this->tag;
    }

    /**
     * 
     * @return string
     */
    public function getColor() {
        return $this->color;
    }
    
    /**
     *
     * @param string $id
     * @return JobInterface $this
     */
    public function setId($id): JobTagInterface{
        $this->id = $id;
        return $this;
    }
        
    /**
     *
     * @param string $tag
     * @return JobTagInterface $this
     */
    public function setTag( $tag): JobTagInterface {
        $this->tag = $tag;
        return $this;
    }

    /**
     * 
     * @param string $color
     * @return JobTagInterface
     */
    public function setColor($color): JobTagInterface {
        $this->color = $color;
        return $this;
    }
}
