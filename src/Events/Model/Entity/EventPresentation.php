<?php


namespace Events\Model\Entity;

use Model\Entity\EntityAbstract;
use Model\Entity\EntityGeneratedKeyInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class EventPresentation extends EntityAbstract implements EventPresentationInterface {

    private $keyAttribute = 'id';

    private $id;
    private $show;
    private $platform;
    private $url;
    private $eventIdFk;

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getShow(): bool {
        return $this->show;      // tinyint
    }

    public function getPlatform(): ?string {
        return $this->platform;
    }

    public function getUrl(): ?string {
        return $this->url;
    }

    public function getEventIdFk(): ?int {
        return $this->eventIdFk;
    }

    public function setId($id): EventPresentationInterface {
        $this->id = $id;
        return $this;
    }

    public function setShow(bool $show): EventPresentationInterface {
        $this->show = $show;
        return $this;
    }

    public function setPlatform($platform = null): EventPresentationInterface {
        $this->platform = $platform;
        return $this;
    }

    public function setUrl($url = null): EventPresentationInterface {
        $this->url = $url;
        return $this;
    }

    public function setEventIdFk($eventIdFk = mull): EventPresentationInterface {
        $this->eventIdFk = $eventIdFk;
        return $this;
    }


}
