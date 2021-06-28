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
        if (isset($article)) { // menu item aktivní (publikovaný)
            /** @var ArticleInterface $article */
            if ($viewModel->isArticleEditable() AND $viewModel->isEditableByUser()) {  // editační režim// uživatel má právo editovat
                $ret = Html::tag('article', ['data-red-renderer'=>'ArticleRenderer', "data-red-datasource"=> "paper {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                            Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/{$article->getId()}"],
                                Html::tag('article', ['id'=>'article_'.$article->getId(), 'class'=>'edit-html'], $article->getArticle())
                            )
                        );
            } else {
                $ret = Html::tag('article', ['data-red-renderer'=>'ArticleRenderer', "data-red-datasource"=> "paper {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                            Html::tag('article', ['class'=>''], $article->getArticle())
                        );
            }
        }
        return $ret ?? '';
    }
}