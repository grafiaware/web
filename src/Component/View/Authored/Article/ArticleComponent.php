<?php
namespace Component\View\Authored\Article;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Article\ArticleViewModelInterface;

use Component\ViewModel\Authored\TemplatedViewModelInterface;

use Component\Renderer\Html\Authored\Article\SelectArticleTemplateRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Authored\EmptyRenderer;

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
        if ($this->contextData->isArticleEditable() AND $this->contextData->userCanEdit()) {
            // zvolit šablonu lze jen dokud je article prázdný a nemá nastavenou šablonu
            // Dokud je article prázdný, zobrazuje se jen toolbar s volbou šablony (SelectArticleTemplateRenderer). Jedna ze šablon musí být prázdná šablona, jinak se nedá pokročit v dialogu.
            // Volba prázdné šablony však může znamenat prázdný obsah pokud šablona nebude obsahovat žádný text.
            if ($this->contextData->hasContent() OR $this->contextData->getContentTemplateName()) {
                $this->setRendererName(ArticleRendererEditable::class);
            } else {
                $this->setRendererName(SelectArticleTemplateRenderer::class);
            }
        } elseif ($this->contextData->hasContent()) {
                $this->setRendererName(ArticleRenderer::class);
        } else {
            $this->setRendererName(EmptyRenderer::class);
        }
    }

}
