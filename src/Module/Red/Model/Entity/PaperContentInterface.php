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
interface PaperContentInterface extends EntityInterface {

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

    public function getUpdated(): \DateTimeInterface;

    public function getActual();

    public function setPaperIdFk($paperIdFk): PaperContentInterface;

    public function setId($id): PaperContentInterface;

    public function setList($list): PaperContentInterface;

    public function setContent($content): PaperContentInterface;

    public function setTemplateName($templateName): PaperContentInterface;

    public function setTemplate($template): PaperContentInterface;

    public function setActive($active): PaperContentInterface;

    public function setPriority($priority): PaperContentInterface;

    public function setShowTime(\DateTimeInterface $showTime=null): PaperContentInterface;

    public function setHideTime(\DateTimeInterface $hideTime=null): PaperContentInterface;

    public function setEventStartTime(\DateTimeInterface $eventStartTime=null): PaperContentInterface;

    public function setEventEndTime(\DateTimeInterface $eventEndTime=null): PaperContentInterface;

    public function setEditor($editor): PaperContentInterface;

    public function setUpdated(\DateTimeInterface $updated): PaperContentInterface;

    public function setActual($actual): PaperContentInterface;
}
