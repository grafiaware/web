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
interface ArticleInterface extends EntityInterface {

    public function getId();

    public function getMenuItemIdFk();

    public function getArticle();

    public function getTemplate();

    public function getEditor();

    public function getUpdated();

    public function getKeyAttribute();

    public function setId($id): ArticleInterface;

    public function setMenuItemIdFk($menuItemIdFk): ArticleInterface;

    public function setArticle($article): ArticleInterface;

    public function setTemplate($template): ArticleInterface;

    public function setEditor($editor): ArticleInterface;
    
    public function setUpdated($updated): ArticleInterface;

}
