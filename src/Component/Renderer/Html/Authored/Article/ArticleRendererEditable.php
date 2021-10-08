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
        $selectTemplate = (string) $viewModel->getContextVariable('selectTemplate') ?? '';

        if (isset($article)) { // menu item aktivní (publikovaný)
            $templateName = $article->getTemplate() ?? '';
            /** @var ArticleInterface $article */
            if ($templateName) {
                $formContent = [
                            Html::tag('input', ['type'=>'hidden', 'name'=>'article_'.$article->getId()]),  // hidden input pro Article Controler updateHtml::tag('div', ['id'=>'article_'.$article->getId()],
                            Html::tag('article',
                                    [
                                        'id'=>'article_'.$article->getId(),
                                        'class'=>'edit-html',
                                        "data-templatename"=>$templateName,   // toto je selektor pro template css - nastaveno v base-template.less souboru
                                    ],
                                    $article->getContent()),  // co je editovatelné je dáno šablonou
                            ];
            } else {
                $formContent = [
                            Html::tag('input', ['type'=>'hidden', 'name'=>'article_'.$article->getId()]),  // hidden input pro Article Controler update
                            Html::tag('article',
                                    [
                                        'id'=>'article_'.$article->getId(),
                                        'class'=>'edit-html',
                                        "data-templatename"=>$templateName,   // toto je selektor pro template css - nastaveno v base-template.less souboru
                                    ],
                                    $article->getContent()),  // editovatelný celý obsah pokud nebyla použita šablona
                            ];
            }
            $ret = Html::tag('section', [
                                        'data-red-renderer'=>'ArticleRendererEditable',
                                        "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}",
                                        ],
                        [
                            $buttonEditContent,
                            $selectTemplate ?? '',
                            Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/{$article->getId()}"],
                                $formContent
                            )
                        ]
                    );
        }
        return $ret ?? '';
    }
}