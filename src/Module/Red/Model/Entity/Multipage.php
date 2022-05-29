<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of Article
 *
 * @author pes2704
 */
class Multipage extends EntityAbstract implements MultipageInterface {

    private $id;
    private $menuItemIdFk;
    private $template;
    private $editor;
    private $updated;

    public function getId() {
        return $this->id;
    }

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getEditor() {
        return $this->editor;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setId($id): MultipageInterface {
        $this->id = $id;
        return $this;
    }

    public function setMenuItemIdFk($menuItemIdFk): MultipageInterface {
        $this->menuItemIdFk = $menuItemIdFk;
        return $this;
    }

    public function setTemplate($template): MultipageInterface {
        $this->template = $template;
        return $this;
    }

    public function setEditor($editor): MultipageInterface {
        $this->editor = $editor;
        return $this;
    }

    public function setUpdated($updated): MultipageInterface {
        $this->updated = $updated;
        return $this;
    }

}
