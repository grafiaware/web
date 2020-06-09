<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\HierarchyNodeInterface;

/**
 * Description of Menutem
 *
 * @author pes2704
 */
class MenuItem extends EntityAbstract implements MenuItemInterface {

//      menu_item.lang_code_fk, menu_item.uid_fk, menu_item.type_fk, menu_item.id, menu_item.title, menu_item.active, menu_item.show_time, menu_item.hide_time,
//	(ISNULL(menu_item.show_time) OR menu_item.show_time<=CURDATE()) AND (ISNULL(menu_item.hide_time) OR CURDATE()<=menu_item.hide_time) AS actual
    private $uidFk;
    private $langCodeFk;
    private $type;
    private $id;
    private $title;  //nazev
    private $active;  //aktiv
    private $showTime;
    private $hideTime;
    private $actual;  //aktual

    public function getUidFk() {
        return $this->uidFk;
    }

    public function getLangCodeFk() {
        return $this->langCodeFk;
    }

    public function getTypeFk() {
        return $this->type;
    }
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getActive() {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function getActual() {
        return $this->actual;
    }

    /**
     * @return \DateTime
     */
    public function getShowTime() {
        return $this->showTime;
    }

    /**
     * @return \DateTime
     */
    public function getHideTime() {
        return $this->hideTime;
    }

    public function setUidFk($uidFk): MenuItemInterface {
        $this->uidFk = $uidFk;
        return $this;
    }

    public function setLangCodeFk($lang): MenuItemInterface {
        $this->langCodeFk = $lang;
        return $this;
    }

    public function setType($type): MenuItemInterface {
        $this->type = $type;
        return $this;
    }

    public function setId($id): MenuItemInterface {
        $this->id = $id;
        return $this;
    }

    public function setTitle($title): MenuItemInterface {
        $this->title = $title;
        return $this;
    }

    public function setActive($active): MenuItemInterface {
        $this->active = $active;
        return $this;
    }

    public function setShowTime($start=null): MenuItemInterface {
        $this->showTime = $start;
        return $this;
    }

    public function setHideTime($stop=null): MenuItemInterface {
        $this->hideTime = $stop;
        return $this;
    }

    public function setActual($actual): MenuItemInterface {
        $this->actual = $actual;
        return $this;
    }
}
