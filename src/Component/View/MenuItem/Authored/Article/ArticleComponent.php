<?php
namespace Component\View\MenuItem\Authored\Article;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;
use Component\ViewModel\MenuItem\Authored\Article\ArticleViewModelInterface;

use Component\Renderer\Html\Authored\SelectTemplateRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRenderer;
use Component\Renderer\Html\Authored\Article\ArticleRendererEditable;
use Component\Renderer\Html\Authored\EmptyContentRenderer;

use Component\View\Manage\EditContentSwitchComponent;

use Access\Enum\AccessPresentationEnum;

/**
 * Description of ArticleComponent
 *
 * @author pes2704
 */
class ArticleComponent extends AuthoredComponentAbstract implements ArticleComponentInterface {
    // hodnoty těchto konstant určují, jaká budou jména proměnných genrovaných template rendererem při renderování php template
    // - např, hodnota const QQQ='nazdar' způsobí, že obsah bude v proměnné $nazdar
    const CONTENT = 'content';

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
        if($this->getStatus()->presentEditableContent()) {
                if($this->isAllowedToPresent(AccessPresentationEnum::EDIT)) {
                // zvolit šablonu lze jen dokud je article prázdný a nemá nastavenou šablonu
                // Dokud je article prázdný, zobrazuje se toolbar s volbou šablony (SelectArticleTemplateRenderer). Jedna ze šablon musí být prázdná šablona, nelze pokračovat bez zvolení šablony.
                // Volba prázdné šablony však může znamenat prázdný obsah pokud šablona nebude obsahovat žádný text.
                if($this->contextData->getAuthoredContentAction()) {
                    if (!$this->contextData->hasContent()) {
                        $this->getComponentView(self::SELECT_TEMPLATE)->setRendererName(SelectTemplateRenderer::class);
                    }
                    $this->setRendererName(ArticleRendererEditable::class);
                } else {
                    $this->setRendererName(ArticleRenderer::class);
                }
                }
        } elseif ($this->contextData->hasContent()) {
                $this->setRendererName(ArticleRenderer::class);
        } else {
            $this->setRendererName(EmptyContentRenderer::class);
        }
    }

}
