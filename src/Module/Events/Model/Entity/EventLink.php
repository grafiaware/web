<?php

namespace Events\Model\Entity;

use Events\Model\Entity\EventLinkInterface;
use Model\Entity\PersistableEntityAbstract;

/**
 * Description of Link
 *
 * @author vlse2610
 */
class EventLink extends PersistableEntityAbstract implements EventLinkInterface {
    private $id;    // NOT NULL
    private $show;  // NOT NULL
    private $href;  // NOT NULL
    private $linkPhaseIdFk; // NOT NULL

    public function getId()  {
        return $this->id;
    }

    public function getShow()  {
        return $this->show;
    }

    public function getHref() {
        return $this->href;
    }

    public function getLinkPhaseIdFk()  {
        return $this->linkPhaseIdFk;
    }

    public function setId($id) :EventLinkInterface {
        $this->id = $id;
        return $this;
    }

    public function setShow($show) :EventLinkInterface{
        $this->show = $show;
        return $this;
    }

    public function setHref($href) :EventLinkInterface{
        $this->href = $href;
        return $this;
    }

    public function setLinkPhaseIdFk($linkPhaseIdFk) :EventLinkInterface {
        $this->linkPhaseIdFk = $linkPhaseIdFk;
        return $this;
    }

}
