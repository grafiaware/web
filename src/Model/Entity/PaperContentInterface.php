<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

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

    public function getActive();

    public function getShowTime();

    public function getHideTime();

    public function getEditor();

    public function getUpdated();

    public function getActual();

    public function setPaperIdFk($paperIdFk): PaperContentInterface;

    public function setId($id): PaperContentInterface;

    public function setList($list): PaperContentInterface;

    public function setContent($content): PaperContentInterface;

    public function setActive($active): PaperContentInterface;

    public function setShowTime($showTime): PaperContentInterface;

    public function setHideTime($hideTime): PaperContentInterface;

    public function setEditor($editor): PaperContentInterface;

    public function setUpdated($updated): PaperContentInterface;

    public function setActual($actual): PaperContentInterface;
}
