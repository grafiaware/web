<?php
namespace Component\ViewModel\Authored\Article;

use Component\ViewModel\Authored\TemplatedViewModelInterface;
use Red\Model\Entity\ArticleInterface;

/**
 *
 * @author pes2704
 */
interface ArticleViewModelInterface {

    /**
     * Vrací Article, pokud existuje a je aktivní (zveřejněný) nebo prezentace je v editačním režimu.
     *
     * @return ArticleInterface|null
     */
    public function getArticle(): ?ArticleInterface;
}
