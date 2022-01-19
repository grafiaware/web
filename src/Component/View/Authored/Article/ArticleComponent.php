<?php
namespace Component\View\Authored\Article;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Article\ArticleViewModelInterface;

use Component\ViewModel\Manage\ToggleEditContentButtonViewModel;

use Component\Renderer\Html\Authored\Article\SelectArticleTemplateRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\View\Manage\ToggleEditContentButtonComponent;

use Red\Model\Enum\AuthoredEnum;
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

    const SELECT_TEMPLATE = 'selectTemplate';

    /**
     *
     * @var ArticleViewModelInterface
     */
    protected $contextData;

    /**
     * Přetěžuje metodu View. Pokud je eneruje PHP template z názvu template a použije ji.
     */
    public function beforeRenderingHook(): void {
        if ($this->contextData->presentEditableContent()) {

            // zvolit šablonu lze jen dokud je article prázdný a nemá nastavenou šablonu
            // Dokud je article prázdný, zobrazuje se toolbar s volbou šablony (SelectArticleTemplateRenderer). Jedna ze šablon musí být prázdná šablona, nelze pokračovat bez zvolení šablony.
            // Volba prázdné šablony však může znamenat prázdný obsah pokud šablona nebude obsahovat žádný text.
            if ($this->userPerformActionWithItem()) {
                if (!$this->hasContent()) {
                    $this->appendComponentView($this->createCompositeViewWithRenderer(SelectArticleTemplateRenderer::class), self::SELECT_TEMPLATE);
                }
                $this->setRendererName(ArticleRendererEditable::class);
            } else {
                $this->setRendererName(ArticleRenderer::class);
            }
            // vytvoří komponent - view s buttonem ButtonEditContent
            $buttonEditContentComponent = new ToggleEditContentButtonComponent($this->configuration);
            $buttonEditContentComponent->setData($this->contextData);
            $buttonEditContentComponent->setRendererContainer($this->rendererContainer);
            $this->appendComponentView($buttonEditContentComponent, self::BUTTON_EDIT_CONTENT);

        } elseif ($this->hasContent()) {
                $this->setRendererName(ArticleRenderer::class);
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
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
