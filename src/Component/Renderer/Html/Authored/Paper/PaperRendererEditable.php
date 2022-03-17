<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\Authored\AuthoredRendererAbstract;

use Component\View\MenuItem\Authored\Paper\PaperComponent;

use Component\ViewModel\MenuItem\Authored\AuthoredViewModelInterface;
use Component\ViewModel\MenuItem\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRendererEditable  extends AuthoredRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $article = $viewModel->getPaper();  // vrací PaperAggregate

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templatePaper')],
                    Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(PaperComponent::BUTTON_EDIT_CONTENT) ?? '',
//                            $this->renderSelectTemplate($viewModel),
                            $viewModel->getContextVariable(PaperComponent::SELECT_TEMPLATE) ?? '',
                            $this->renderRibbon($viewModel),
                            $viewModel->getContextVariable(PaperComponent::CONTENT) ?? '',   // obsah z papet komponenty
                        ]
                    )
                );
        return $html ?? '';
    }

####################################
    protected function renderEditControlButtons(AuthoredViewModelInterface $viewModel): string {
        /** @var PaperViewModelInterface $viewModel */
        $authoredId = $viewModel->getAuthoredContentId();
        $onclick = (string) "togleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)
        $buttons1 = [];

        $buttons1[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> 'Vybrat šablonu stránky',
                'data-position'=>'top right',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"",
                'onclick'=>$onclick
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.template')])
            );
        $buttons1[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> 'Odstranit šablonu stránky',
                'data-position'=>'top right',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"red/v1/paper/$authoredId/templateremove",
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.templateremove')])
            );

        $paper = $viewModel->getPaper();

        $buttons2 = [];

        if ($paper instanceof PaperAggregatePaperContentInterface AND $paper->getPaperContentsArray()) {
            $buttons2[] = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrange')])
                );
        } else {
            $buttons2[] =  Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Přidat sekci',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$authoredId/section",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowdown')])
                    )
                );
        }

        return $this->renderButtonsDiv($buttons1).$this->renderButtonsDiv($buttons2);
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