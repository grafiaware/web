<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use DateTime;

/**
 * Description of Article
 *
 * @author pes2704
 */
class Paper extends PersistableEntityAbstract implements PaperInterface {

    private $id;
    private $menuItemIdFk;
    private $list;
    private $headline;
    private $perex;
    private $template;
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
    public function getPerex() {
        return $this->perex;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getEditor() {
        return $this->editor;
    }

    /**
     * Hodnota může být null po insertu do db, po kterém nenásledovalo zpětné načtení dat.
     *
     * @return DateTime|null
     */
    public function getUpdated(): ?DateTime {
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

    public function setPerex($perex): PaperInterface {
        $this->perex = $perex;
        return $this;
    }

    public function setTemplate($template): PaperInterface {
        $this->template = $template;
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

    /**
     * Hodnota může být null po insertu do db, po kterém nenásledovalo zpětné načtení dat.
     *
     * @param DateTime $updated
     * @return PaperInterface
     */
    public function setUpdated(DateTime $updated=null): PaperInterface {
        $this->updated = $updated;
        return $this;
    }
}
