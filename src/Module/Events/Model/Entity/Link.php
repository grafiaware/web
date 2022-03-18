<?php

namespace Events\Model\Entity;

use Events\Model\Entity\LinkInterface;
use Model\Entity\EntityAbstract;

/**
 * Description of Link
 *
 * @author vlse2610
 */
class Link extends EntityAbstract implements LinkInterface {
    private $id;
    private $show;
    private $href;
    private $linkPhaseIdFk;
    
    
    private $keyAttribute = 'id';
        
    public function getKeyAttribute() {
        return $this->keyAttribute;
    }
    
    
    
    public function getId() : ?int  {
        return $this->id;
    }

    public function getShow() : ?int  {
        return $this->show;
    }

    public function getHref() : ?string {
        return $this->href;
    }

    public function getLinkPhaseIdFk(): ?int  {
        return $this->linkPhaseIdFk;
    }

    public function setId($id) :LinkInterface {
        $this->id = $id;
        return $this;
    }

    public function setShow($show) :LinkInterface{
        $this->show = $show;
        return $this;
    }

    public function setHref( string $href=null) :LinkInterface{
        $this->href = $href;
        return $this;
    }

    public function setLinkPhaseIdFk($linkPhaseIdFk) :LinkInterface {
        $this->linkPhaseIdFk = $linkPhaseIdFk;
        return $this;
    }

}
