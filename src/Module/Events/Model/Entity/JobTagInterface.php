<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface JobTagInterface extends PersistableEntityInterface {
    
    
    public function getId()  ;
    
    /**
     * 
     * @return string
     */
    public function getTag() ;

    /**
     * @return string
     */
    public function getColor();
    
     /**
     *
     * @param string $id
     * @return JobTagInterface $this
     */
    public function setId($id) : JobTagInterface;
    
    
    /**
     * 
     * @param string $tag
     * @return JobTagInterface $this
     */
    public function setTag( $tag) : JobTagInterface ;
       
    /**
     * 
     * @param string $color
     * @return JobTagInterface
     */
    public function setColor($color): JobTagInterface;    
    
}
