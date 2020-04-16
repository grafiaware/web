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

    public function getLangCode();

    public function getMenuItemIdFk();

    public function getList();

    public function getHeadline();

    public function getContent();

    public function getKeywords();

    public function getEditor();

    public function getUpdated();

    public function setLangCode($langCodeFk);

    public function setMenuItemIdFk($uidFk);

    public function setList($list);

    public function setHeadline($headline);

    public function setContent($content);

    public function setKeywords($keywords);

    public function setEditor($editor);

    public function setUpdated($updated);

}
