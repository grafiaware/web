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

    private $menuItemId;
    private $langCode;
    private $list;
    private $headline;
    private $content;
    private $keywords;
    private $editor;
    private $updated;

    public function getMenuItemIdFk() {
        return $this->menuItemId;
    }

    public function getLangCode() {
        return $this->langCode;
    }

    public function getList() {
        return $this->list;
    }

    public function getHeadline() {
        return $this->headline;
    }

    public function getContent() {
        return $this->content;
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

    public function setMenuItemIdFk($uidFk) {
        $this->menuItemId = $uidFk;
        return $this;
    }

    public function setLangCode($langCodeFk) {
        $this->langCode = $langCodeFk;
        return $this;
    }

    public function setList($list) {
        $this->list = $list;
        return $this;
    }

    public function setHeadline($headline) {
        $this->headline = $headline;
        return $this;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setKeywords($keywords) {
        $this->keywords = $keywords;
        return $this;
    }

    public function setEditor($editor) {
        $this->editor = $editor;
        return $this;
    }

    public function setUpdated($updated) {
        $this->updated = $updated;
        return $this;
    }
}
