<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Red\Model\Entity\StaticItemInterface;
use Model\Entity\PersistableEntityInterface;
use DateTime;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface StaticItemInterface extends PersistableEntityInterface {

    public function getId();

    public function getMenuItemIdFk();

    public function getPath();

    public function getTemplate();

    public function getCreator();

    public function getUpdated(): ?DateTime;

    public function setId($id): StaticItemInterface;

    public function setMenuItemIdFk($uidFk): StaticItemInterface;

    public function setPath($path): StaticItemInterface;

    public function setTemplate($template): StaticItemInterface;

    public function setCreator($editor): StaticItemInterface;

    public function setUpdated(?DateTime $updated=null): StaticItemInterface;

}
