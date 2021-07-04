<?php
namespace Component\Renderer\Html\Authored\Article;

use Red\Model\Entity\ArticleInterface;
use Component\Renderer\Html\HtmlModelRendererAbstract;

use Component\ViewModel\Authored\Article\ArticleViewModelInterface;
use Pes\Text\Html;
use Pes\View\Renderer\ImplodeRenderer;


/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRenderer extends HtmlModelRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací ArticleInterface
        $ret = Html::tag('article', ['data-red-renderer'=>'ArticleRenderer', "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                    $article->getContent()
                );
        return $ret ?? '';
    }
}