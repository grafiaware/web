<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface NetworkInterface extends PersistableEntityInterface {
    
    
    public function getId()  ;
    
    /**
     * 
     * @return string
     */
    public function getIcon() ;

    /**
     * @return string
     */
    public function getEmbedCodeTemplate();
    
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
    public function setIcon( $tag) : JobTagInterface ;
       
    /**
     * 
     * @param string $color
     * @return JobTagInterface
     */
    public function setEmbedCodeTemplate($color): JobTagInterface;    
    
}
