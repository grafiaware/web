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
class PaperContent extends EntityAbstract implements PaperContentInterface {

    private $id;
    private $paperIdFk;
    private $list;
    private $content;
    private $template;
    private $active;
    private $priority;
    private $showTime;
    private $hideTime;
    private $eventTime;
    private $editor;
    private $updated;
    private $actual;

    private $keyAttribute = ['id'];

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

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

    public function getTemplate() {
        return $this->template;
    }

    public function getActive() {
        return $this->active;
    }

    public function getPriority() {
        return $this->priority;
    }

    public function getShowTime(): ?\DateTimeInterface {
        return $this->showTime;
    }

    public function getHideTime(): ?\DateTimeInterface {
        return $this->hideTime;
    }

    public function getEventTime(): ?\DateTimeInterface {
        return $this->eventTime;
    }

    public function getEditor() {
        return $this->editor;
    }

    public function getUpdated(): \DateTimeInterface {
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

    public function setTemplate($template): PaperContentInterface {
        $this->template = $template;
        return $this;
    }

    public function setActive($active): PaperContentInterface {
        $this->active = $active;
        return $this;
    }

    public function setPriority($priority): PaperContentInterface {
        $this->priority = $priority;
        return $this;
    }

    public function setShowTime(\DateTimeInterface $showTime=null): PaperContentInterface {
        $this->showTime = $showTime;
        return $this;
    }

    public function setHideTime(\DateTimeInterface $hideTime=null): PaperContentInterface {
        $this->hideTime = $hideTime;
        return $this;
    }

    public function setEventTime(\DateTimeInterface $eventTime=null): PaperContentInterface {
        $this->eventTime = $eventTime;
        return $this;
    }

    public function setEditor($editor): PaperContentInterface {
        $this->editor = $editor;
        return $this;
    }

    public function setUpdated(\DateTimeInterface $updated): PaperContentInterface {
        $this->updated = $updated;
        return $this;
    }

    public function setActual($actual): PaperContentInterface {
        $this->actual = $actual;
        return $this;
    }

}
