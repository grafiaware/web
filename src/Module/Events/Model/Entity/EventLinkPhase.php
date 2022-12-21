<?php
namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\EventLinkPhaseInterface;

/**
 * Description of EventLinkPhase
 *
 * @author vlse2610
 */
class EventLinkPhase extends PersistableEntityAbstract implements EventLinkPhaseInterface {

    private $id;
    private $text;

    public function getId()  {
        return $this->id;
    }
    public function getText()  {
        return $this->text;
    }

    public function setId($id) :EventLinkPhaseInterface {
        $this->id = $id;
        return $this;
    }

    public function setText(  $text=null) :EventLinkPhaseInterface {
        $this->text = $text;
        return $this;
    }

}
