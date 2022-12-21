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
    
    private $tag;

    /**
     *
     * @return string
     */
    public function getTag() : string  {
        return $this->tag;
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
