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
        $active = $viewModel->isMenuItemActive();
        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';
        $selectTemplate = (string) $viewModel->getContextVariable('selectTemplate') ?? '';
        $articleButtonsForm = $this->renderArticleButtonsForm($article);

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
                                       'data-tooltip'=> $active ? "published" : "not published",
                                       ],
                                        Html::tag('i',
                                           [
                                           'class'=> $this->classMap->resolve($active, 'Content','i1.published', 'i1.notpublished'),
                                           ]
                                        )
                                    )
                                )
                                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.nameMenuItem')],
                                    Html::tag('p', ['class'=>''],
                                        'Typ: Article'
                                    )
                                    .Html::tag('p', ['class'=>''],
                                        'Název položky menu'
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
    
     private function renderArticleButtonsForm(ArticleInterface $article) {

        $btnAktivni =  Html::tag('button', [
                    'class'=>$this->classMap->get('PaperButtons', 'button'),
                    'data-tooltip'=> 'Publikovat / Nepublikovat', //$active ? 'Nepublikovat' : 'Publikovat',
                    'data-position'=>'top right',
                    'tabindex'=>'0',
                    'formtarget'=>'_self',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('CommonButtons', 'button.notpublish')])
                    //Html::tag('i', ['class'=>$this->classMapEditable->resolve($active, 'CommonButtons', 'button.notpublish', 'button.publish')])
                );
        $btnDoKose =   Html::tag('button', [
                    'class'=>$this->classMap->get('PaperButtons', 'button'),
                    'data-tooltip'=> 'Odstranit položku',
                    'data-position'=>'top right',
                    'tabindex'=>'0',
                    'formtarget'=>'_self',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    'onclick'=>"return confirm('Jste si jisti?');"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('CommonButtons', 'button.movetotrash')])
                );
        
        
        return Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttonsWrap')],
                Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttons')],
                    $btnAktivni.$btnDoKose
                )
            )
        );
     }
}