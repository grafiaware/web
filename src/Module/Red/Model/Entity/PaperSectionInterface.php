<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityInterface;

/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface PaperSectionInterface extends EntityInterface {

    public function getPaperIdFk();

    public function getId();

    public function getList();

    public function getContent();

    public function getTemplateName();

    public function getTemplate();

    public function getActive();

    public function getPriority();

    public function getShowTime(): ?\DateTimeInterface;

    public function getHideTime(): ?\DateTimeInterface;

    public function getEventStartTime(): ?\DateTimeInterface;

    public function getEventEndTime(): ?\DateTimeInterface;

    public function getEditor();

    public function getUpdated(): ?\DateTimeInterface;

    public function getActual();

    public function setPaperIdFk($paperIdFk): PaperSectionInterface;

    public function setId($id): PaperSectionInterface;

    public function setList($list): PaperSectionInterface;

    public function setContent($content): PaperSectionInterface;

    public function setTemplateName($templateName): PaperSectionInterface;

    public function setTemplate($template): PaperSectionInterface;

    public function setActive($active): PaperSectionInterface;

    public function setPriority($priority): PaperSectionInterface;

    public function setShowTime(\DateTimeInterface $showTime=null): PaperSectionInterface;

    public function setHideTime(\DateTimeInterface $hideTime=null): PaperSectionInterface;

    public function setEventStartTime(\DateTimeInterface $eventStartTime=null): PaperSectionInterface;

    public function setEventEndTime(\DateTimeInterface $eventEndTime=null): PaperSectionInterface;

    public function setEditor($editor): PaperSectionInterface;

    public function setUpdated(\DateTimeInterface $updated=null): PaperSectionInterface;

    public function setActual($actual): PaperSectionInterface;
}
