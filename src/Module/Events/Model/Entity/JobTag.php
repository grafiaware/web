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
    
    private $id;    
    private $tag;
    
    
    
    public function getId()  {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getTag() : string  {
        return $this->tag;
    }

    
    /**
     *
     * @param type $id
     * @return JobInterface
     */
    public function setId($id) : JobTagInterface{
        $this->id = $id;
        return $this;
    }
    
    
    /**
     *
     * @param string $tag
     * @return JobTagInterface
     */
    public function setTag( $tag=null) : JobTagInterface {
        $this->tag = $tag;
        return $this;
    }




}
