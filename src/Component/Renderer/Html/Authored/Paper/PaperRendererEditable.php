<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\Authored\AuthoredRendererAbstract;

use Component\View\Authored\AuthoredComponentAbstract;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Pes\Text\Html;

use Component\View\Authored\Paper\PaperComponent;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRendererEditable  extends AuthoredRendererAbstract {
    public function renderOLD(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paper = $viewModel->getPaper();  // vrací PaperAggregate
        $menuItem = $viewModel->getMenuItem();

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.templatePaper')],
                    Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$paper->getId()} for item {$paper->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $this->renderSelectTemplate($viewModel),
                            $this->renderRibbon($viewModel),
                            $viewModel->getContextVariable(PaperComponent::CONTENT) ?? '',
                        ]
                    )
                );
        return $html ?? '';
    }

    #############################

    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $article = $viewModel->getPaper();  // vrací PaperAggregate

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.templatePaper')],
                    Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $this->renderSelectTemplate($viewModel),
                            $this->renderRibbon($viewModel),
                            $viewModel->getContextVariable(PaperComponent::CONTENT) ?? '',   // obsah z papet komponenty
                        ]
                    )
                );
        return $html ?? '';
    }

####################################

    protected function renderContentControlButtons(PaperViewModelInterface $viewModel): array {
        $templateName = $viewModel->getAuthoredTemplateName() ?? '';
        $paper = $viewModel->getPaper();
        $onclick = (string) "togleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)
        $buttons = [];

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

        if ($paper instanceof PaperAggregatePaperContentInterface AND $paper->getPaperContentsArray()) {
            $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->get('PaperButtons', 'button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('PaperButtons', 'button.arrange')])
                );
        } else {
            $buttons[] =  Html::tag('button',
                    ['class'=>$this->classMap->get('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat sekci',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperId/section",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowdown')])
                    )
                );
        }

        return $buttons;
    }

//    private function renderRibbon(PaperViewModelInterface $viewModel) {
//        $menuItem = $viewModel->getMenuItem();
//        $type = $viewModel->getItemType();  // spoléhám na to, že návratová hodnota je hodnota z AuthoredTypeEnum
//        return
//            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.ribbon-paper')], //lepítko s buttony
//                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')], //aktivní/neaktivní paper
//                    Html::tag('div', ['class'=> 'ikona-popis', 'data-tooltip'=> $menuItem->getActive() ? "published" : "not published"],
//                        Html::tag('i', ['class'=> $this->classMap->resolve($menuItem->getActive(), 'Content','i1.published', 'i1.notpublished')])
//                    )
//                )
//                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.nameMenuItem')],
//                    Html::tag('p', ['class'=>''],
//                        $type
//                        .Html::tag('span', ['class'=>''],$menuItem->getTitle())
//                    )
//                )
//                .$this->renderPaperButtonsForm($viewModel)
//            );
//    }

//    private function renderSelectTemplate(PaperViewModelInterface $viewModel) {
//        $paper = $viewModel->getPaper();
//        $contentTemplateName = $paper->getTemplate();
//        $paperId = $paper->getId();
//
//        return
//            // id je parametr pro togleTemplateSelect(id) - voláno onclick na button 'Vybrat šablonu stránky'
//            Html::tag('div', ['id'=>"select_template_paper_$paperId",'class'=>$this->classMap->get('PaperTemplateSelect', 'div.selectTemplate')],
//                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
//                    [
////                        Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName]),
//                        // class je třída pro selector v tinyInit var selectTemplateConfig
//                        Html::tag('div', ['id'=>"paper_$paperId", 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplatePaper')],''),
//                    ]
//                )
//            );
//    }

//    private function renderPaperButtonsForm(PaperViewModelInterface $viewModel) {
//        $paper = $viewModel->getPaper();
//        $menuItem = $viewModel->getMenuItem();
//        $active = $menuItem->getActive();
//        $paperId = $paper->getId();
//
//        $buttons = [];
//
//        $btnAktivni = Html::tag('button',
//                ['class'=>$this->classMap->get('CommonButtons', 'button'),
//                'data-tooltip'=> $active ? 'Nepublikovat' : 'Publikovat',
//                'data-position'=>'top right',
//                'formmethod'=>'post',
//                'formaction'=>"red/v1/menu/{$menuItem->getUidFk()}/toggle",
//                ],
//                Html::tag('i', ['class'=>$this->classMap->resolve($active, 'CommonButtons', 'button.notpublish', 'button.publish')])
//            );
//        $btnDoKose = Html::tag('button', [
//                    'class'=>$this->classMap->get('PaperButtons', 'button'),
//                    'data-tooltip'=> 'Odstranit položku',
//                    'data-position'=>'top right',
//                    'formtarget'=>'_self',
//                    'formmethod'=>'post',
//                    'formaction'=>"red/v1/hierarchy/{$menuItem->getUidFk()}/trash",
//                    'onclick'=>"return confirm('Jste si jisti?');"
//                    ],
//                    Html::tag('i', ['class'=>$this->classMap->get('CommonButtons', 'button.movetotrash')])
//                );
//        $buttons[] =  Html::tag('button', [
//                    'class'=>$this->classMap->get('PaperButtons', 'button.template'),
//                    'data-tooltip'=> 'Vybrat šablonu stránky',
//                    'data-position'=>'top right',
//                    'formtarget'=>'_self',
//                    'formmethod'=>'post',
//                    'formaction'=>"",
//                    'onclick'=>"togleTemplateSelect(event, 'select_template_paper_$paperId');"
//                    ],
//                    Html::tag('i', ['class'=>$this->classMap->get('PaperButtons', 'button.template i')])
//                );
//        if ($paper instanceof PaperAggregatePaperContentInterface AND $paper->getPaperContentsArray()) {
//            $buttons[] = Html::tag('button', [
//                    'class'=>$this->classMap->get('PaperButtons', 'button'),
//                    'data-tooltip'=> 'Seřadit podle data',
//                    'data-position'=>'top right',
//                    'formmethod'=>'post',
//                    'formaction'=>"",
//                    ],
//                    Html::tag('i', ['class'=>$this->classMap->get('PaperButtons', 'button.arrange')])
//                );
//        } else {
//            $buttons[] =  Html::tag('button',
//                        ['class'=>$this->classMap->get('ContentButtons', 'button'),
//                        'data-tooltip'=>'Přidat obsah',
//                        'type'=>'submit',
//                        'name'=>'button',
//                        'value' => '',
//                        'formmethod'=>'post',
//                        'formaction'=>"red/v1/paper/$paperId/content",
//                        ],
//                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
//                            Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.addcontent')])
//                            .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowdown')])
//                        )
//                    );
//        }
//
//        return Html::tag('form', ['method'=>'POST', 'action'=>""],
//            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttonsWrap')],
//                Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttons')],
//                    $btnAktivni.$btnDoKose
//                )
//                .Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttons')],
//                    implode('', $buttons)
//                )
//            )
//        );
//    }

//    protected function renderNewContent(PaperAggregatePaperContentInterface $paperAggregate) {
//        $paperId = $paperAggregate->getId();
//
//        return
//        Html::tag('div', ['class'=>$this->classMap->get('Content', 'div div.ribbon')],
//            $this->getNewContentButtonsForm($paperAggregate)
//        )
//        .Html::tag('form',
//            ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/contents"],
//            Html::tag('content',
//                [
//                    'id' => "new content_for_paper_$paperId",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
//                    'class'=>$this->classMap->get('Content', 'form content'),
//                    'data-paperowner'=>$paperAggregate->getEditor()
//                ],
//                "Nový obsah"
//            )
//        )
//        ;
//    }
//
//    private function getNewContentButtonsForm(PaperAggregatePaperContentInterface $paperAggregate) {
//        $paperId = $paperAggregate->getId();
//
//        return
//        Html::tag('form', ['method'=>'POST', 'action'=>""],
//            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.page')],
//                Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
//                    Html::tag('button',
//                        ['class'=>$this->classMap->get('ContentButtons', 'button'),
//                        'data-tooltip'=>'Přidat obsah',
//                        'type'=>'submit',
//                        'name'=>'button',
//                        'value' => '',
//                        'formmethod'=>'post',
//                        'formaction'=>"red/v1/paper/$paperId/contents",
//                        ],
//                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
//                            Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.addcontent')])
//                            .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowdown')])
//                        )
//                    )
//                )
//            )
//        );
//    }
}