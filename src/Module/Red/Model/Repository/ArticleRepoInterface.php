<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Red\Model\Entity\ArticleInterface;

/**
 *
 * @author pes2704
 */
interface ArticleRepoInterface extends RepoAssotiatedOneInterface {
    public function get($id): ?ArticleInterface;
    public function add(ArticleInterface $article);
    public function remove(ArticleInterface $article);
}
