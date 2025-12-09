<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Red\Model\Entity\StaticItemInterface;
use Model\Entity\PersistableEntityAbstract;
use DateTime;

/**
 * Description of StaticClass. Třída je pojmenovína takto, protože jméno Static nelze použít - klíčové slovo v PHP.
 *
 * @author pes2704
 */
class StaticItemClass extends PersistableEntityAbstract implements StaticItemInterface {

    private $id;
    private $menuItemIdFk;
    private $path;
    private $template;
    private $creator;
    private $updated;

    public function getId() {
        return $this->id;
    }

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getPath() {
        return $this->path;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getCreator() {
        return $this->creator;
    }
    
    /**
     * Hodnota může být null po insertu do db, po kterém nenásledovalo zpětné načtení dat.
     *
     * @return DateTime|null
     */
    public function getUpdated(): ?DateTime {
        return $this->updated;
    }

    public function setId($id): StaticItemInterface {
        $this->id = $id;
        return $this;
    }

    public function setMenuItemIdFk($menuItemIdFk): StaticItemInterface {
        $this->menuItemIdFk = $menuItemIdFk;
        return $this;
    }

    public function setPath($path): StaticItemInterface {
        $this->path = $path;
        return $this;
    }

    public function setTemplate($template): StaticItemInterface {
        $this->template = $template;
        return $this;
    }

    public function setCreator($creator): StaticItemInterface {
        $this->creator = $creator;
        return $this;
    }
    
    /**
     * 
     * @param DateTime $updated
     * @return StaticItemInterface
     */
    public function setUpdated(DateTime $updated=null): StaticItemInterface {
        $this->updated = $updated;
        return $this;
    }
}
