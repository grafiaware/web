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
class PaperSection extends EntityAbstract implements PaperSectionInterface {

    private $id;
    private $paperIdFk;
    private $list;
    private $content;
    private $templateName;
    private $template;
    private $active;
    private $priority;
    private $showTime;
    private $hideTime;
    private $eventStartTime;
    private $eventEndTime;
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

    public function getTemplateName() {
        return $this->templateName;
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

    public function getEventStartTime(): ?\DateTimeInterface {
        return $this->eventStartTime;
    }

    public function getEventEndTime(): ?\DateTimeInterface {
        return $this->eventEndTime;
    }

    public function getEditor() {
        return $this->editor;
    }

    public function getUpdated(): ?\DateTimeInterface {
        return $this->updated;
    }

    public function getActual() {
        return $this->actual;
    }

    public function setId($id): PaperSectionInterface {
        $this->id = $id;
        return $this;
    }

    public function setPaperIdFk($paperIdFk): PaperSectionInterface {
        $this->paperIdFk = $paperIdFk;
        return $this;
    }

    public function setList($list): PaperSectionInterface {
        $this->list = $list;
        return $this;
    }

    public function setContent($content): PaperSectionInterface {
        $this->content = $content;
        return $this;
    }

    public function setTemplateName($templateName): PaperSectionInterface {
        $this->templateName = $templateName;
        return $this;
    }

    public function setTemplate($template): PaperSectionInterface {
        $this->template = $template;
        return $this;
    }

    public function setActive($active): PaperSectionInterface {
        $this->active = $active;
        return $this;
    }

    public function setPriority($priority): PaperSectionInterface {
        $this->priority = $priority;
        return $this;
    }

    public function setShowTime(\DateTimeInterface $showTime=null): PaperSectionInterface {
        $this->showTime = $showTime;
        return $this;
    }

    public function setHideTime(\DateTimeInterface $hideTime=null): PaperSectionInterface {
        $this->hideTime = $hideTime;
        return $this;
    }

    public function setEventStartTime(\DateTimeInterface $eventStartTime=null): PaperSectionInterface {
        $this->eventStartTime = $eventStartTime;
        return $this;
    }

    public function setEventEndTime(\DateTimeInterface $eventEndTime=null): PaperSectionInterface {
        $this->eventEndTime = $eventEndTime;
        return $this;
    }

    public function setEditor($editor): PaperSectionInterface {
        $this->editor = $editor;
        return $this;
    }

    public function setUpdated(\DateTimeInterface $updated=null): PaperSectionInterface {
        $this->updated = $updated;
        return $this;
    }

    public function setActual($actual): PaperSectionInterface {
        $this->actual = $actual;
        return $this;
    }

}
