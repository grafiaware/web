<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of Article
 *
 * @author pes2704
 */
class Paper extends EntityAbstract implements PaperInterface {

    private $id;
    private $menuItemIdFk;
    private $list;
    private $headline;
    private $keywords;
    private $editor;
    private $updated;

    public function getId() {
        return $this->id;
    }

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getList() {
        return $this->list;
    }

    public function getHeadline() {
        return $this->headline;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getEditor() {
        return $this->editor;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setId($id): PaperInterface {
        $this->id = $id;
        return $this;
    }
    
    public function setMenuItemIdFk($uidFk): PaperInterface {
        $this->menuItemIdFk = $uidFk;
        return $this;
    }

    public function setList($list): PaperInterface {
        $this->list = $list;
        return $this;
    }

    public function setHeadline($headline): PaperInterface {
        $this->headline = $headline;
        return $this;
    }

    public function setKeywords($keywords): PaperInterface {
        $this->keywords = $keywords;
        return $this;
    }

    public function setEditor($editor): PaperInterface {
        $this->editor = $editor;
        return $this;
    }

    public function setUpdated($updated): PaperInterface {
        $this->updated = $updated;
        return $this;
    }
}
