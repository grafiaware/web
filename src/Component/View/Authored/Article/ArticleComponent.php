<?php
namespace Component\View\Authored\Article;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Article\ArticleViewModelInterface;

use Component\ViewModel\Authored\TemplatedViewModelInterface;

use Component\Renderer\Html\Authored\Article\SelectArticleTemplateRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class ArticleComponent extends AuthoredComponentAbstract implements ArticleComponentInterface {
    //TODO:
//    vytvořit ArticleComponent (a interface)
//    ?? Menu přesunout?
//    ContentComponent -> TemplateSelectComponent
//    AuthoredComponentAbstract o úroveň výš


    /**
     *
     * @var ArticleViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Generuje PHP template z názvu template objektu Paper a použije ji.
     */
    public function beforeRenderingHook(): void {
        if ($this->contextData->presentEditableArticle() AND $this->contextData->userCanEdit()) {

            // zvolit šablonu lze jen dokud je article prázdný a nemá nastavenou šablonu
            // Dokud je article prázdný, zobrazuje se jen toolbar s volbou šablony (SelectArticleTemplateRenderer). Jedna ze šablon musí být prázdná šablona, nelze pokračovat bez zvolení šablony.
            // Volba prázdné šablony však může znamenat prázdný obsah pokud šablona nebude obsahovat žádný text.
            if ($this->hasContent()) {
                $this->setRendererName(ArticleRendererEditable::class);
            } else {
                $this->setRendererName(SelectArticleTemplateRenderer::class);
            }
        } elseif ($this->hasContent()) {
                $this->setRendererName(ArticleRenderer::class);
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
    }

    private function getContentTemplateName() {
        $article = $this->contextData->getArticle();
        return isset($article) ? $article->getTemplate() : null;
    }

    /**
     * Informuje, jestli article má zobrazitelná obsah. Zobrazitelný obsah má article. jehož metoda getContent() vrací neprázdný řetězec.
     *
     * @return bool
     */
    private function hasContent(): bool {
        $article = $this->contextData->getArticle();
        return (isset($article) AND $article->getContent()) ? true : false; //content může být null
    }

}
