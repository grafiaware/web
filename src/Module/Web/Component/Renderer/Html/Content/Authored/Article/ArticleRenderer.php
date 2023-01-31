<?php
namespace Web\Component\Renderer\Html\Content\Authored\Article;

use Red\Model\Entity\ArticleInterface;

use Web\Component\ViewModel\Content\Authored\Article\ArticleViewModelInterface;
use Pes\Text\Html;
use Web\Component\Renderer\Html\HtmlRendererAbstract;

use Web\Component\View\Content\Authored\AuthoredComponentAbstract;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací ArticleInterface

        $b = $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT);
        $c = $article->getContent();
        $i = $article->getId();
        $ui = $article->getMenuItemIdFk();
        $impl = implode([$b, $c]);

        $ret = Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateArticle')],
                    Html::tag('article', ['data-red-renderer'=>'ArticleRenderer', "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $article->getContent(),
                        ]
                    )
                );
        return $ret ?? '';
    }
}