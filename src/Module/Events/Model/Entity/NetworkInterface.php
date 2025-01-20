<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;


/**
 *
 * @author vlse2610
 */
interface NetworkInterface extends PersistableEntityInterface {
    
    /**
     * @return string
     */
    public function getId()  ;
    
    /**
     * 
     * @return string
     */
    public function getIcon();
    
    /**
     * @return string
     */
    public function getTitle();
    
    /**
     * @return string
     */
    public function getEmbedCodeTemplate();
    
     /**
     *
     * @param string $id
     * @return NetworkInterface $this
     */
    public function setId($id) : NetworkInterface;
    
    
    /**
     * 
     * @param string $tag
     * @return NetworkInterface $this
     */
    public function setIcon( $tag) : NetworkInterface ;
    
    /**
     * 
     * @param type $title
     * @return NetworkInterface
     */
    public function setTitle($title): NetworkInterface ;       

    /**
     * 
     * @param string $color
     * @return NetworkInterface
     */
    public function setEmbedCodeTemplate($color): NetworkInterface;    
    
}
