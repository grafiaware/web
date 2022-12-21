<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use DateTime;
/**
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface ArticleInterface extends PersistableEntityInterface {

    public function getId();

    public function getMenuItemIdFk();

    public function getContent();

    public function getTemplate();

    public function getEditor();

    public function getUpdated(): ?DateTime;

    public function setId($id): ArticleInterface;

    public function setMenuItemIdFk($menuItemIdFk): ArticleInterface;

    public function setContent($article): ArticleInterface;

    public function setTemplate($template): ArticleInterface;

    public function setEditor($editor): ArticleInterface;

    public function setUpdated(DateTime $updated=null): ArticleInterface;

}
