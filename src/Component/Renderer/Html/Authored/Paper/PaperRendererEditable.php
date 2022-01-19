<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Pes\Text\Html;

use Component\View\Authored\Paper\PaperComponent;
use Component\View\Authored\AuthoredComponentAbstract;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRendererEditable  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
        $active = $viewModel->isMenuItemActive();
        $buttonEditContent = (string) $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '';

        $selectTemplate = $this->renderSelectTemplate($paperAggregate);
        $paperButtonsForm = $this->renderPaperButtonsForm($paperAggregate);
        $inner = (string) $viewModel->getContextVariable(PaperComponent::CONTEXT_TEMPLATE) ?? '';  // Paper Component beforeRenderingHook()
        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.templatePaper')],
                    Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                        [
                            $buttonEditContent,
                            $selectTemplate,
                            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.ribbon')], //lepítko s buttony
                                $paperButtonsForm
                            ),
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
                            ),
    //                        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/{$paperAggregate->getId()}"],
                                $inner
    //                        ),
                        ]
                    )
                );
        return $html ?? '';
    }

    private function renderSelectTemplate(PaperInterface $paper) {
        $contentTemplateName = $paper->getTemplate();
        $paperId = $paper->getId();

        return
            // id je parametr pro togleTemplateSelect(id) - voláno onclick na button 'Vybrat šablonu stránky'
            Html::tag('div', ['id'=>"select_template_paper_$paperId",'class'=>$this->classMap->get('PaperTemplateSelect', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
                    [
                        Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName]),
                        // class je třída pro selector v tinyInit var selectTemplateConfig
                        Html::tag('div', ['id'=>"paper_$paperId", 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplatePaper')],''),
                    ]
                )
            );
    }

    private function renderPaperButtonsForm(PaperInterface $paper) {
        $paperId = $paper->getId();

        $buttons = [];
        $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->get('PaperButtons', 'button.template'),
                    'data-tooltip'=> 'Vybrat šablonu stránky',
                    'data-position'=>'top right',
                    'tabindex'=>'0',
                    'formtarget'=>'_self',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    'onclick'=>"togleTemplateSelect(event, 'select_template_paper_$paperId');"
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
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperId/content",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowdown')])
                        )
                    );
        }

        return Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttonsWrap')],
                Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.buttons')],
                    implode('', $buttons)
                )
            )
        );
    }

################## ??

    /**
     * headline semafor a form
     *
     * @param MenuItemPaperAggregateInterface $paper
     * @return type
     */


    private function getTrashContentForm($paperContent) {
        return
            Html::tag('section', ['class'=>$this->classMap->get('Content', 'section.trash')],
                Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.ribbon')],
                    $this->getTrashButtons($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')],
                        Html::tag('i',['class'=>$this->classMap->get('Content', 'i.trash')])
                )
                .Html::tag('div',
                    [
                        'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->get('Content', 'div.trash_content'),
                        'data-owner'=>$paperContent->getEditor()
                    ],
                    $paperContent->getContent()
                    )
            );
    }

    private function getContentButtons(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $show = $paperContent->getShowTime();
        $hide = $paperContent->getHideTime();
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();
        $active = $paperContent->getActive();
        $actual = $paperContent->getActual();

        if (isset($show)) {
            $showTime = $show->format("d.m.Y") ;
            if (isset($hide)) {
                $hideTime = $hide->format("d.m.Y");
                $textZobrazeni = "Zobrazeno od $showTime  do $hideTime";
            } else {
                $hideTime = '';
                $textZobrazeni = "Zobrazeno od $showTime";
            }
        } elseif (isset($hide)) {
            $showTime = '';
            $hideTime = $hide->format("d.m.Y");
            $textZobrazeni = "Zobrazeno do $hideTime";
        } else {
            $showTime = '';
            $hideTime = '';
            $textZobrazeni = "Zobrazeno trvale";
        }

        return

        Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.wrapContent')],
            Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->get('ContentButtons', 'button'),
                    'data-tooltip'=>'Aktivní/neaktivní obsah',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->resolve($active, 'ContentButtons', 'button.notpublish', 'button.publish')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->get('ContentButtons', 'button.date'),
                    'data-tooltip'=> $textZobrazeni,
                    'data-position'=>'top right',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.changedate')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->get('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu výš',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/up",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowup')])
                    )
                )
                .Html::tag('button',
                    ['class'=>$this->classMap->get('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu níž',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/down",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowdown')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->get('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah před',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_above",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowup')])
                    )
                )
                .Html::tag('button',
                    ['class'=>$this->classMap->get('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah za',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_below",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowdown')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->get('ContentButtons', 'button'),
                    'data-tooltip'=>'Do koše',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/trash",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.movetotrash')])
                )
            )
        )
        .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsDate')],
            Html::tag('button', [
                'class'=>$this->classMap->get('ContentButtons', 'button'),
                'data-tooltip'=>'Trvale',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'permanent',
                'formmethod'=>'post',
                'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                ],
                Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.permanently')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->get('ContentButtons', 'button'),
                'data-tooltip'=>'Uložit',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'calendar',
                'formmethod'=>'post',
                'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                ],
                Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.save')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->get('ContentButtons', 'button.content'),
                'data-tooltip'=>'Zrušit úpravy',
                'data-position'=>'top right',
                'onclick'=>"event.preventDefault(); this.form.reset();"
                ],
                Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.cancel')])
            )
            .Html::tag('div', [
                'class'=>$this->classMap->get('ContentButtons', 'button'),
                'data-position'=>'top right',
                ],
                Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.changedate')])
            )
        );
//        .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.wrapDate')],
//            Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.wrapKalendar'), ],
//                    Html::tag('p', ['class'=>$this->classMap->get('ContentButtons', 'p')], 'Uveřejnit od')
//                    .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.calendar')],
//                        Html::tag('div',['class'=>$this->classMap->get('ContentButtons', 'div.input')],
//                            Html::tagNopair('input', ['type'=>'text', 'name'=>"show_$paperContentId", 'placeholder'=>'Klikněte pro výběr data', 'value'=>$showTime])
//                        )
//                     )
//                    .Html::tag('p', ['class'=>$this->classMap->get('ContentButtons', 'p')], 'Uveřejnit do')
//                    .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.calendar')],
//                        Html::tag('div',['class'=>$this->classMap->get('ContentButtons', 'div.input')],
//                        Html::tagNopair('input', ['type'=>'text', 'name'=>"hide_$paperContentId", 'placeholder'=>'Klikněte pro výběr data', 'value'=> $hideTime])
//                    )
//                )
//            )
//        );
    }

    private function getTrashButtons(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return
            Html::tag('div', ['class'=>$this->classMap->get('TrashButtons', 'div.wrapTrash')],
                Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('ContentButtons', 'button'),
                        'data-tooltip'=>'Obnovit',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/restore",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('TrashButtons', 'button.restore')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('ContentButtons', 'button'),
                        'data-tooltip'=>'Smazat',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/delete",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('TrashButtons', 'button.delete')])
                    )
                )
            );

    }

    protected function renderNewContent(PaperAggregatePaperContentInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('div', ['class'=>$this->classMap->get('Content', 'div div.ribbon')],
            $this->getNewContentButtonsForm($paperAggregate)
        )
        .Html::tag('form',
            ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/contents"],
            Html::tag('content',
                [
                    'id' => "new content_for_paper_$paperId",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMap->get('Content', 'form content'),
                    'data-paperowner'=>$paperAggregate->getEditor()
                ],
                "Nový obsah"
            )
        )
        ;
    }

    private function getNewContentButtonsForm(PaperAggregatePaperContentInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->get('PaperButtons', 'div.page')],
                Html::tag('div', ['class'=>$this->classMap->get('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->get('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperId/contents",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->get('ContentButtons', 'button.arrowdown')])
                        )
                    )
                )
            )
        );
    }
}