<?php
namespace Component\Renderer\Html\Authored\Article;

use Red\Model\Entity\ArticleInterface;

use Component\ViewModel\Authored\Article\ArticleViewModelInterface;
use Pes\Text\Html;
use Component\Renderer\Html\HtmlRendererAbstract;


/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací ArticleInterface
        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';

        $ret = Html::tag('article', ['data-red-renderer'=>'ArticleRenderer', "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                    [$buttonEditContent, $article->getContent()]
                );
        return $ret ?? '';
    }
}