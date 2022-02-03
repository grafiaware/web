<?php
namespace Component\Renderer\Html\Authored\Article;

use Red\Model\Entity\ArticleInterface;
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Enum\AuthoredTypeEnum;

use Component\View\Authored\AuthoredComponentAbstract;
use Component\ViewModel\Authored\Article\ArticleViewModelInterface;

use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Pes\Text\Html;
use Component\Renderer\Html\HtmlRendererAbstract;


/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRendererEditable extends HtmlRendererAbstract {
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
        $menuItem = $viewModel->getMenuItem();

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.templatePaper')],
                    Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $this->renderSelectTemplate($viewModel),
                            $this->renderRibbon($viewModel),
                            $article->getContent(),
                        ]
                    )
                );
        return $html ?? '';
    }

    private function renderRibbon(AuthoredViewModelInterface $viewModel) {
        $menuItem = $viewModel->getMenuItem();
        $type = $viewModel->getItemType();  // spoléhám na to, že návratová hodnota je hodnota z AuthoredTypeEnum
        switch ($type) {
            case AuthoredTypeEnum::ARTICLE:
                $class = $this->classMap->get('PaperButtons', 'div.ribbon-article');
                break;
            case AuthoredTypeEnum::PAPER:
                $class = $this->classMap->get('PaperButtons', 'div.ribbon-paper');
                break;
            case AuthoredTypeEnum::MULTIPAGE:
                $class = $this->classMap->get('PaperButtons', 'div.ribbon-multipage');
                break;
            default:
                break;
        }
        return
            Html::tag('div', ['class'=>$class], //lepítko s buttony
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')], //aktivní/neaktivní paper
                    Html::tag('div', ['class'=> 'ikona-popis', 'data-tooltip'=> $menuItem->getActive() ? "published" : "not published"],
                        Html::tag('i', ['class'=> $this->classMap->resolve($menuItem->getActive(), 'Content','i1.published', 'i1.notpublished')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.nameMenuItem')],
                    Html::tag('p', ['class'=>''],
                        $type
                    )
                    .Html::tag('p', ['class'=>''],
                        $menuItem->getTitle()
                    )
                )
                .$this->renderArticleButtonsForm($viewModel)
            );
    }

    private function renderSelectTemplate(AuthoredViewModelInterface $viewModel) {
        $type = $viewModel->getItemType();
        $contentTemplateName = $viewModel->getAuthoredTemplateName();
        $authoredContentId = $viewModel->getAuthoredContentId();

        $templateNamePostVar = "template_$authoredContentId";
        $templateContentPostVar = "{$type}_{$authoredContentId}";
        return
            // id je parametr pro togleTemplateSelect(id) - voláno onclick na button 'Vybrat šablonu stránky'
            Html::tag('div', ['id'=> $this->getTemplateSelectId($viewModel),'class'=>$this->classMap->get('PaperTemplateSelect', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/$type/$authoredContentId/template"],
                    [
                        Html::tagNopair('input', ["type"=>"hidden", "name"=>$templateNamePostVar, "value"=>$contentTemplateName]),
                        // class je třída pro selector v tinyInit var selectTemplateConfig
//                        Html::tag('div', ['id'=>$templateContentPostVar, 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplateArticle')],''),
                        Html::tag('div', ['id'=>$templateContentPostVar, 'class'=>"tiny_select_template_$type"],''),
                    ]
                )
            );
    }

    private function getTemplateSelectId(AuthoredViewModelInterface $viewModel) {
        $type = $viewModel->getItemType();
        $articleId = $viewModel->getAuthoredContentId(); //$article->getId();
        return "select_template_{$type}_{$articleId}";
    }

####################################
    private function renderArticleButtonsForm(AuthoredViewModelInterface $viewModel) {
        $menuItem = $viewModel->getMenuItem();
        $active = $menuItem->getActive();
        $type = $viewModel->getItemType();
        $onclick = (string) "togleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)

        $btnAktivni = Html::tag('button',
                ['class'=>$this->classMap->get('CommonButtons', 'button'),
                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
                'data-position'=>'top right',
                'type'=>'submit',
                'formmethod'=>'post',
                'formaction'=>"red/v1/menu/{$menuItem->getUidFk()}/toggle",
                ],
                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'CommonButtons', 'button.notpublish', 'button.publish')])
            );

        $btnDoKose = Html::tag('button', [
                    'class'=>$this->classMap->get('PaperButtons', 'button'),
                    'data-tooltip'=> 'Odstranit položku',
                    'data-position'=>'top right',
                    'formtarget'=>'_self',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/trash",
                    'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('CommonButtons', 'button.movetotrash')])
                );

        $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->get('PaperButtons', 'button.template'),
                    'data-tooltip'=> 'Vybrat šablonu stránky',
                    'data-position'=>'top right',
                    'formtarget'=>'_self',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    'onclick'=>$onclick
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('PaperButtons', 'button.template i')])
                );

        return Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttonsWrap')],
                Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttons')],
                    $btnAktivni.$btnDoKose
                )
                .Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttons')],
                    implode('', $buttons)
                )
            )
        );
    }


}