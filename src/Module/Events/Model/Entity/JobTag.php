<?php
namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Events\Model\Entity\JobTagInterface;


/**
 * Description of JobTag
 *
 * @author vlse2610
 */
class JobTag  extends EntityAbstract implements JobTagInterface {
    private $id;
    private $tag;
    
    private $keyAttribute = 'id';
        
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    /**
     * 
     * @return int
     */
    public function getId() :int {
        return $this->id;
    }
    /**
     * 
     * @return string|null
     */
    public function getTag() : ?string {
        return $this->tag;
    }
    
    /**
     * 
     * @param type $id
     * @return JobTagInterface
     */
    public function setId($id) : JobTagInterface {
        $this->id = $id;
        return $this;
    }
    
    /**
     * 
     * @param string $tag
     * @return JobTagInterface
     */
    public function setTag(string $tag=null) : JobTagInterface {
        $this->tag = $tag;
        return $this;
    }


    
    
}
