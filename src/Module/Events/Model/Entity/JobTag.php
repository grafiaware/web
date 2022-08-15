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

    /**
     *
     * @return string|null
     */
    public function getTag()  {
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
