<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\NetworkInterface;


/**
 * Description of JobTag
 *
 * @author vlse2610
 */
class Network  extends PersistableEntityAbstract implements NetworkInterface {
    
    private $id;     //NOT NULL
    private $icon;    //NOT NULL
    private $embedCodeTemplate;
    
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
    public function getIcon()  {
        return $this->icon;
    }

    /**
     * 
     * @return string
     */
    public function getEmbedCodeTemplate() {
        return $this->embedCodeTemplate;
    }
    
    /**
     *
     * @param string $id
     * @return NetworkInterface $this
     */
    public function setId($id): NetworkInterface{
        $this->id = $id;
        return $this;
    }
        
    /**
     *
     * @param string $icon
     * @return NetworkInterface $this
     */
    public function setIcon( $icon): NetworkInterface {
        $this->icon = $icon;
        return $this;
    }

    /**
     * 
     * @param string $embedCodeTemplate
     * @return NetworkInterface
     */
    public function setEmbedCodeTemplate($embedCodeTemplate): NetworkInterface {
        $this->embedCodeTemplate = $embedCodeTemplate;
        return $this;
    }
}
