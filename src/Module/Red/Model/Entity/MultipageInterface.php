<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityInterface;
use DateTime;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface MultipageInterface extends EntityInterface {

    public function getId();

    public function getMenuItemIdFk();

    public function getTemplate();

    public function getEditor();

    public function getUpdated(): ?DateTime;

    public function setId($id): MultipageInterface;

    public function setMenuItemIdFk($menuItemIdFk): MultipageInterface;

    public function setTemplate($template): MultipageInterface;

    public function setEditor($editor): MultipageInterface;

    public function setUpdated(DateTime $updated=null): MultipageInterface;

}
