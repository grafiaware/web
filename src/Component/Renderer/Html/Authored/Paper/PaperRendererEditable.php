<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Pes\Text\Html;

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
        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';

        $selectTemplate = $this->renderSelectTemplate($paperAggregate);
        $paperButtonsForm = $this->renderPaperButtonsForm($paperAggregate);
        $inner = (string) $viewModel->getContextVariable('template') ?? '';
        $html =
                Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                    [
                        $buttonEditContent,
                        $selectTemplate,
                        Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.corner')], //lepítko s buttony
                            $paperButtonsForm
                        ),
                        Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')], //aktivní/neaktivní paper
                            Html::tag('div',
                               [
                               'class'=> 'ikona-popis',
                               'data-tooltip'=> $active ? "published" : "not published",
                               ],
                                Html::tag('i',
                                   [
                                   'class'=> $this->classMap->resolveClass($active, 'Content','i1.published', 'i1.notpublished'),
                                   ]
                                )
                            )
                        ),
                        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/{$paperAggregate->getId()}"],
                            $inner
                        ),
                    ]
                );
        return $html ?? '';
    }

    private function renderSelectTemplate(PaperInterface $paper) {
        $contentTemplateName = $paper->getTemplate();
        $paperId = $paper->getId();

        return
//                Html::tag('div', [],
//                    Html::tag('button', [
//                        'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div button'),
//                        'formtarget'=>'_self',
//                        'tabindex'=>'0'
//                        ],
//                        Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.hidden')], 'Šablony pro stránku')
//                        .Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.visible')],
//                            Html::tag('i', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div i')])
//                        )
//                    )
//                    .
                    Html::tag('div', ['id'=>"select_template_paper_$paperId",'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.selectTemplate')],
                        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
                            Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName])
                            .
                            Html::tag('div', ['id'=>"paper_$paperId", 'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.tinyPaperSelect')],'')
                        )

                    );
//                );
    }

    private function renderPaperButtonsForm(PaperInterface $paper) {
        $paperId = $paper->getId();

        $buttons = [];
        $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->getClass('PaperButtons', 'button.template'),
                    'data-tooltip'=> 'Vybrat šablonu stránky',
                    'data-position'=>'top right',
                    'tabindex'=>'0',
                    'formtarget'=>'_self',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    'onclick'=>"togleTemplateSelect('select_template_paper_$paperId'); event.preventDefault();"
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperButtons', 'button.template i')])
        );
        if ($paper instanceof PaperAggregatePaperContentInterface AND $paper->getPaperContentsArray()) {
            $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->getClass('PaperButtons', 'button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperButtons', 'button.arrange')])
                );
        } else {
            $buttons[] =  Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperId/content",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    );
        }

        return Html::tag('form', ['method'=>'POST', 'action'=>""],
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.buttonsWrap')],
                Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.buttons')],
                    implode('', $buttons)
                )
            )
        );
    }
    /**
     * headline semafor a form
     *
     * @param MenuItemPaperAggregateInterface $paper
     * @return type
     */


    private function getTrashContentForm($paperContent) {
        return
            Html::tag('section', ['class'=>$this->classMap->getClass('Content', 'section.trash')],
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.corner')],
                    $this->getTrashButtons($paperContent)
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')],
                        Html::tag('i',['class'=>$this->classMap->getClass('Content', 'i.trash')])
                )
                .Html::tag('div',
                    [
                        'id' => "content_{$paperContent->getId()}",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                        'class'=>$this->classMap->getClass('Content', 'div.trash_content'),
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

        Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapContent')],
            Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Aktivní/neaktivní obsah',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => 'toggle',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/toggle",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->resolveClass($active, 'ContentButtons', 'button.notpublish', 'button.publish')])
                )
                .Html::tag('button', [
                    'class'=>$this->classMap->getClass('ContentButtons', 'button.date'),
                    'data-tooltip'=> $textZobrazeni,
                    'data-position'=>'top right',
                    'onclick'=>'event.preventDefault();'
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.changedate')])
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu výš',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/up",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowup')])
                    )
                )
                .Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Posunout o jednu níž',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/down",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movecontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah před',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_above",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowup')])
                    )
                )
                .Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Přidat další obsah za',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/add_below",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                    )
                )
            )
            .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                Html::tag('button',
                    ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                    'data-tooltip'=>'Do koše',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/trash",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.movetotrash')])
                )
            )
        )
        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsDate')],
            Html::tag('button', [
                'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                'data-tooltip'=>'Trvale',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'permanent',
                'formmethod'=>'post',
                'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                ],
                Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.permanently')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                'data-tooltip'=>'Uložit',
                'data-position'=>'top right',
                'type'=>'submit',
                'name'=>'button',
                'value' => 'calendar',
                'formmethod'=>'post',
                'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/actual",
                ],
                Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.save')])
            )
            .Html::tag('button', [
                'class'=>$this->classMap->getClass('ContentButtons', 'button.content'),
                'data-tooltip'=>'Zrušit úpravy',
                'data-position'=>'top right',
                'onclick'=>"event.preventDefault(); this.form.reset();"
                ],
                Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.cancel')])
            )
            .Html::tag('div', [
                'class'=>$this->classMap->getClass('ContentButtons', 'button'),
                'data-position'=>'top right',
                ],
                Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.changedate')])
            )
        )
        .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapDate')],
            Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.wrapKalendar'), ],
                    Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'p')], 'Uveřejnit od')
                    .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.calendar')],
                        Html::tag('div',['class'=>$this->classMap->getClass('ContentButtons', 'div.input')],
                            Html::tagNopair('input', ['type'=>'text', 'name'=>"show_$paperContentId", 'placeholder'=>'Klikněte pro výběr data', 'value'=>$showTime])
                        )
                     )
                    .Html::tag('p', ['class'=>$this->classMap->getClass('ContentButtons', 'p')], 'Uveřejnit do')
                    .Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.calendar')],
                        Html::tag('div',['class'=>$this->classMap->getClass('ContentButtons', 'div.input')],
                        Html::tagNopair('input', ['type'=>'text', 'name'=>"hide_$paperContentId", 'placeholder'=>'Klikněte pro výběr data', 'value'=> $hideTime])
                    )
                )
            )
        );
    }

    private function getTrashButtons(PaperContentInterface $paperContent) {
        //TODO: atributy data-tooltip a data-position jsou pro semantic - zde jsou napevno zadané
        $paperIdFk = $paperContent->getPaperIdFk();
        $paperContentId = $paperContent->getId();

        return
            Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.wrapTrash')],
                Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('TrashButtons', 'button'),
                        'data-tooltip'=>'Obnovit',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/restore",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('TrashButtons', 'button.restore')])
                    )
                )
                .Html::tag('div', ['class'=>$this->classMap->getClass('TrashButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('TrashButtons', 'button'),
                        'data-tooltip'=>'Smazat',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperIdFk/content/$paperContentId/delete",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('TrashButtons', 'button.delete')])
                    )
                )
            );

    }

    protected function renderNewContent(PaperAggregatePaperContentInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();

        return
        Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div div.corner')],
            $this->getNewContentButtonsForm($paperAggregate)
        )
        .Html::tag('form',
            ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/contents"],
            Html::tag('content',
                [
                    'id' => "new content_for_paper_$paperId",  // id musí být na stránce unikátní - skládám ze slova content_ a id, v kontroléru lze toto jméno také složit a hledat v POST proměnných
                    'class'=>$this->classMap->getClass('Content', 'form content'),
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
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.page')],
                Html::tag('div', ['class'=>$this->classMap->getClass('ContentButtons', 'div.buttonsContent')],
                    Html::tag('button',
                        ['class'=>$this->classMap->getClass('ContentButtons', 'button'),
                        'data-tooltip'=>'Přidat obsah',
                        'type'=>'submit',
                        'name'=>'button',
                        'value' => '',
                        'formmethod'=>'post',
                        'formaction'=>"red/v1/paper/$paperId/contents",
                        ],
                        Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.icons')],
                            Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.addcontent')])
                            .Html::tag('i', ['class'=>$this->classMap->getClass('ContentButtons', 'button.arrowdown')])
                        )
                    )
                )
            )
        );
    }
}