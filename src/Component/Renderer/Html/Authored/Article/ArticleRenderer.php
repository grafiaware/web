<?php
namespace Component\Renderer\Html\Authored\Article;

use Red\Model\Entity\ArticleInterface;

use Component\ViewModel\Authored\Article\ArticleViewModelInterface;
use Pes\Text\Html;
use Component\Renderer\Html\HtmlRendererAbstract;

use Component\View\Authored\AuthoredComponentAbstract;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací ArticleInterface
        $buttonEditContent = (string) $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '';

        $ret = Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateArticle')],
                    Html::tag('article', ['data-red-renderer'=>'ArticleRenderer', "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [$buttonEditContent, $article->getContent()]
                    )
                );
        return $ret ?? '';
    }
}