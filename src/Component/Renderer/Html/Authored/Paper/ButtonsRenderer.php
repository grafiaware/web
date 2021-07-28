<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;
use Component\Renderer\Html\HtmlRendererAbstract;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Pes\Text\Html;

/**
 * Description of ButtonsRenderer
 *
 * @author pes2704
 */
class ButtonsRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paper = $viewModel->getPaper();
        return $this->renderPaperButtonsForm($paper);

    }

    public function renderPaperButtonsForm(PaperInterface $paper) {
        $paperId = $paper->getId();

        $buttons = [];
        if ($paper instanceof PaperAggregatePaperContentInterface AND $paper->getPaperContentsArray()) {
            $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->getClass('PaperButtons', 'button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"not_implemented",
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
            Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.buttonsPage')],
                    implode('', $buttons)
            )
        );
    }
}
