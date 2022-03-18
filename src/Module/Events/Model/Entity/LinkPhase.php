<?php
namespace Events\Model\Entity;

use Events\Model\Entity\LinkPhaseInterface;
use Model\Entity\EntityAbstract;



/**
 * Description of LinkPhase
 *
 * @author vlse2610
 */
class LinkPhase  extends EntityAbstract implements LinkPhaseInterface{
    private $id;
    private $text;
    
    private $keyAttribute = 'id';
        
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    
    
    
    public function getId(): ?int  {
        return $this->id;
    }

    public function getText(): ?string  {
        return $this->text;
    }

    public function setId($id) :LinkPhaseInterface {
        $this->id = $id;
        return $this;
    }

    public function setText(string $value=null)  :LinkPhaseInterface {
        $this->text = $text;
        return $this;
    }


    
    
}
