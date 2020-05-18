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
interface PaperHeadlineInterface extends EntityInterface {

    public function getMenuItemIdFk();

    public function getList();

    public function getHeadline();

    public function getKeywords();

    public function getEditor();

    public function getUpdated();

    public function setMenuItemIdFk($uidFk): PaperHeadlineInterface;

    public function setList($list): PaperHeadlineInterface;

    public function setHeadline($headline): PaperHeadlineInterface;

    public function setKeywords($keywords): PaperHeadlineInterface;

    public function setEditor($editor): PaperHeadlineInterface;

    public function setUpdated($updated): PaperHeadlineInterface;

}
