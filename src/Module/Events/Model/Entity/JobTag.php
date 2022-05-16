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
    private $tag;
    
    private $keyAttribute = 'tag';
        
    public function getKeyAttribute() {
        return $this->keyAttribute;
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
     * @param string $tag
     * @return JobTagInterface
     */
    public function setTag(string $tag=null) : JobTagInterface {
        $this->tag = $tag;
        return $this;
    }


    
    
}
