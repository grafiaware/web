<?php
namespace Component\Renderer\Html\Authored\Article;

use Component\Renderer\Html\Authored\AuthoredRendererAbstract;

use Red\Model\Entity\ArticleInterface;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Article\ArticleViewModelInterface;

use Red\Middleware\Redactor\Controler\ArticleControler;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRendererEditable extends AuthoredRendererAbstract {
    public function renderOLD(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací ArticleInterface
        $menuItem = $viewModel->getMenuItem();
//        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';
        $buttonEditContent = (string) $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '';
        $selectTemplate = (string) $viewModel->getContextVariable('selectTemplate') ?? '';
        $articleButtonsForm = $this->renderArticleButtonsForm($menuItem, $article);

        if (isset($article)) { // menu item aktivní (publikovaný)
            $templateName = $article->getTemplate() ?? '';
            /** @var ArticleInterface $article */
            if ($templateName) {
                $formContent = [

                                Html::tag('div',
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
                                    Html::tag('div',
                                        [
                                            'id'=>'article_'.$article->getId(),
                                            'class'=>'edit-html',
                                            "data-templatename"=>$templateName,   // toto je selektor pro template css - nastaveno v base-template.less souboru
                                        ],
                                        $article->getContent()),  // editovatelný celý obsah pokud nebyla použita šablona
                            ];
            }
            $ret =
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.templateArticle')],
                    Html::tag('article', [
                                        'data-red-renderer'=>'ArticleRendererEditable',
                                        "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}",
                                        ],
                        [
                            $buttonEditContent,
                            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.ribbon-article')], //lepítko s buttony
                                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')], //aktivní/neaktivní paper
                                    Html::tag('div',
                                       [
                                       'class'=> 'ikona-popis',
                                       'data-tooltip'=> $menuItem->getActive() ? "published" : "not published",
                                       ],
                                        Html::tag('i',
                                           [
                                           'class'=> $this->classMap->resolve($menuItem->getActive(), 'Content','i1.published', 'i1.notpublished'),
                                           ]
                                        )
                                    )
                                )
                                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.nameMenuItem')],
                                    Html::tag('p', ['class'=>''],
                                        'Article'
//                                        . $menuItem->getTitle()
                                    )
                                    .Html::tag('p', ['class'=>''],
                                        $menuItem->getTitle()
                                    )
                                )
                                .$articleButtonsForm

                            ),
                            $selectTemplate ?? '',
                            Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/{$article->getId()}"],
                                $formContent
                            )
                        ]
                        )
                    );
        }
        return $ret ?? '';
    }

    #############################
    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací PaperAggregate

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.templatePaper')],
                    Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $this->renderSelectTemplate($viewModel),
                            $this->renderRibbon($viewModel),
                            $this->getFormWithContent($viewModel),
                        ]
                    )
                );
        return $html ?? '';
    }
    
##### article
    private function getFormWithContent(ArticleViewModelInterface $viewModel) {
        $id = $viewModel->getAuthoredContentId();
        $templateName = $viewModel->getAuthoredTemplateName();

        return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/$id"],
                Html::tag('div',
                    [
                        'id'=> ArticleControler::ARTICLE_CONTENT.$id,           // POZOR - id musí být unikátní - jinak selhává tiny selektor
                        'class'=>'edit-html',
                        "data-templatename"=>$templateName,   // toto je selektor pro template css - nastaveno v base-template.less souboru
                    ],
                     $viewModel->getArticle()->getContent()  // co je editovatelné je dáno šablonou
                )
            );
    }


####################################


    protected function renderContentControlButtons(ArticleViewModelInterface $viewModel): array {
        $templateName = $viewModel->getAuthoredTemplateName() ?? '';
        $onclick = (string) "togleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)
        $buttons = [];
        $disabled = $templateName ? 'disabled' : '';
        $buttons[] = Html::tag('button', [
                'class'=>[$this->classMap->get('PaperButtons', 'button.template'), $disabled],
                'data-tooltip'=> 'Vybrat šablonu stránky',
                'data-position'=>'top right',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"",
                'onclick'=>$onclick
                ],
                Html::tag('i', ['class'=>$this->classMap->get('PaperButtons', 'button.template i')])
            );
        return $buttons;
    }
}