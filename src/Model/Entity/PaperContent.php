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
class PaperContent extends EntityAbstract implements PaperContentInterface {

    private $menuItemIdFk;
    private $id;
    private $list;
    private $content;
    private $editor;
    private $updated;

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getId() {
        return $this->id;
    }

    public function getList() {
        return $this->list;
    }

    public function getContent() {
        return $this->content;
    }

    public function getEditor() {
        return $this->editor;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setMenuItemIdFk($uidFk): PaperContentInterface {
        $this->menuItemIdFk = $uidFk;
        return $this;
    }

    public function setId($id): PaperContentInterface {
        $this->id = $id;
        return $this;
    }

    public function setList($list): PaperContentInterface {
        $this->list = $list;
        return $this;
    }

    public function setContent($content): PaperContentInterface {
        $this->content = $content;
        return $this;
    }

    public function setEditor($editor): PaperContentInterface {
        $this->editor = $editor;
        return $this;
    }

    public function setUpdated($updated): PaperContentInterface {
        $this->updated = $updated;
        return $this;
    }
}
