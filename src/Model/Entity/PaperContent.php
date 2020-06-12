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

    private $id;
    private $paperIdFk;
    private $list;
    private $content;
    private $active;
    private $showTime;
    private $hideTime;
    private $editor;
    private $updated;
    private $actual;

    public function getId() {
        return $this->id;
    }

    public function getPaperIdFk() {
        return $this->paperIdFk;
    }

    public function getList() {
        return $this->list;
    }

    public function getContent() {
        return $this->content;
    }

    public function getActive() {
        return $this->active;
    }

    public function getShowTime() {
        return $this->showTime;
    }

    public function getHideTime() {
        return $this->hideTime;
    }

    public function getEditor() {
        return $this->editor;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function getActual() {
        return $this->actual;
    }

    public function setId($id): PaperContentInterface {
        $this->id = $id;
        return $this;
    }

    public function setPaperIdFk($paperIdFk): PaperContentInterface {
        $this->paperIdFk = $paperIdFk;
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

    public function setActive($active): PaperContentInterface {
        $this->active = $active;
        return $this;
    }

    public function setShowTime($showTime): PaperContentInterface {
        $this->showTime = $showTime;
        return $this;
    }

    public function setHideTime($hideTime): PaperContentInterface {
        $this->hideTime = $hideTime;
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

    public function setActual($actual): PaperContentInterface {
        $this->actual = $actual;
        return $this;
    }

}
