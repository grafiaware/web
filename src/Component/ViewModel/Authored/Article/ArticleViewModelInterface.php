<?php
namespace Component\ViewModel\Authored\Article;

use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Red\Model\Entity\ArticleInterface;

/**
 *
 * @author pes2704
 */
interface ArticleViewModelInterface extends AuthoredViewModelInterface {

    /**
     * Vrací Article, pokud existuje a je aktivní (zveřejněný) nebo prezentace je v editačním režimu.
     *
     * @return ArticleInterface|null
     */
    public function getArticle(): ?ArticleInterface;
}
