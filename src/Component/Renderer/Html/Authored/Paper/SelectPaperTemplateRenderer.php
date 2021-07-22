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
use Red\Model\Entity\PaperContentInterface;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class SelectPaperTemplateRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $templatedContent = $viewModel->offsetExists('templatedContent') ? $viewModel->offsetGet('templatedContent') : '';
        $contentTemplateName = $viewModel->getPaper()->getTemplate();
        $paperId = $viewModel->getPaper()->getId();
        return
            Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
                Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName])
                .
                Html::tag('div', ['id'=>"paper_$paperId", 'class'=>'paper_template_select mceNonEditable'],''
//                            Html::tag('div', ['class'=>'mceNonEditable'], $templatedContent)
                )
            );
    }

    #############################################

    public function renderPaperTemplateButtonsForm(PaperInterface $paper) {
        $paperId = $paper->getId();
        $templateName = $paper->getTemplate();

        $postName = 'folder_'.$paperId;
        $postItems = [
            'Course'=>'course',
            'Contact'=>'contact',
            'Default'=>'default',
            'Test' => 'test'
        ];
        $items = [];
        $class = $this->classMapEditable->getClass('PaperTemplateSelect', 'div.item');
        foreach ($postItems as $title => $value) {
            $items[] = Html::tag('div', ['class'=>$class, 'value'=>$value], $title);
        }
        return
            Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
                Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperTemplateButtons', 'div.paperTemplate'), 'data-tooltip'=>'Výběr šablony stránky'],
                    Html::tag('i', ['class'=>$this->classMapEditable->getClass('PaperTemplateButtons', 'button.templateSelect')])
                    .Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperTemplateSelect', 'div.menu')],
                        Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperTemplateSelect', 'div.header')], 'Vyberte šablonu stránky')
                        .Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperTemplateSelect', 'div.selection')],
                            Html::tag('input', ['class'=>$this->classMapEditable->getClass('PaperTemplateSelect', 'input'), 'type'=>'hidden', 'name'=>$postName, 'onchange'=>'this.form.submit()'] )
                            .Html::tag('i', ['class'=>$this->classMapEditable->getClass('PaperTemplateSelect', 'i.dropdown')])
                            .Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperTemplateSelect', 'div.text')], 'Šablona')
                            .Html::tag('div', ['class'=>$this->classMapEditable->getClass('PaperTemplateSelect', 'div.scrollmenu')],
                                    implode(PHP_EOL, $items)
                            )
                        )
                    )
                )
            );
    }
}
