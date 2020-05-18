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
class PaperHeadline extends EntityAbstract implements PaperHeadlineInterface {

    private $menuItemIdFk;
    private $list;
    private $headline;
    private $keywords;
    private $editor;
    private $updated;

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

    public function setMenuItemIdFk($uidFk): PaperHeadlineInterface {
        $this->menuItemIdFk = $uidFk;
        return $this;
    }

    public function setList($list): PaperHeadlineInterface {
        $this->list = $list;
        return $this;
    }

    public function setHeadline($headline): PaperHeadlineInterface {
        $this->headline = $headline;
        return $this;
    }

    public function setKeywords($keywords): PaperHeadlineInterface {
        $this->keywords = $keywords;
        return $this;
    }

    public function setEditor($editor): PaperHeadlineInterface {
        $this->editor = $editor;
        return $this;
    }

    public function setUpdated($updated): PaperHeadlineInterface {
        $this->updated = $updated;
        return $this;
    }
}
