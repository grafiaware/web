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
interface PaperInterface extends EntityInterface {

    public function getId();

    public function getMenuItemIdFk();

    public function getList();

    public function getHeadline();

    public function getPerex();

    public function getKeywords();

    public function getEditor();

    public function getUpdated();

    public function setId($id): PaperInterface;

    public function setMenuItemIdFk($uidFk): PaperInterface;

    public function setList($list): PaperInterface;

    public function setHeadline($headline): PaperInterface;

    public function setPerex($perex): PaperInterface;

    public function setKeywords($keywords): PaperInterface;

    public function setEditor($editor): PaperInterface;

    public function setUpdated($updated): PaperInterface;

}
