<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;
use DateTime;

/**
 * Description of Article
 *
 * @author pes2704
 */
class Article extends EntityAbstract implements ArticleInterface {

    private $id;
    private $menuItemIdFk;
    private $article;
    private $template;
    private $editor;
    private $updated;

    public function getId() {
        return $this->id;
    }

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getContent() {
        return $this->article;
    }

    public function getTemplate() {
        return $this->template;
    }

    public function getEditor() {
        return $this->editor;
    }

    /**
     * Hodnota může být null po insertu do db, po kterém nenásledovalo zpětné načtení dat.
     *
     * @return DateTime|null
     */
    public function getUpdated(): ?DateTime {
        return $this->updated;
    }

    public function setId($id): ArticleInterface {
        $this->id = $id;
        return $this;
    }

    public function setMenuItemIdFk($menuItemIdFk): ArticleInterface {
        $this->menuItemIdFk = $menuItemIdFk;
        return $this;
    }

    public function setContent($article): ArticleInterface {
        $this->article = $article;
        return $this;
    }

    public function setTemplate($template): ArticleInterface {
        $this->template = $template;
        return $this;
    }

    public function setEditor($editor): ArticleInterface {
        $this->editor = $editor;
        return $this;
    }

    /**
     * Hodnota může být null po insertu do db, po kterém nenásledovalo zpětné načtení dat.
     *
     * @param DateTime $updated
     * @return ArticleInterface
     */
    public function setUpdated(DateTime $updated=null): ArticleInterface {
        $this->updated = $updated;
        return $this;
    }

}
