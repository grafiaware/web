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
class ArticleRendererEditable extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací ArticleInterface
        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';

        if (isset($article)) { // menu item aktivní (publikovaný)
            /** @var ArticleInterface $article */
            $ret = Html::tag('article', ['data-red-renderer'=>'ArticleRendererEditable', "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $buttonEditContent,
                            Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/{$article->getId()}"],
                                Html::tag('div', ['id'=>'article_'.$article->getId(), 'class'=>'edit-html'], $article->getContent())
                            )
                        ]
                    );
        }
        return $ret ?? '';
    }
}