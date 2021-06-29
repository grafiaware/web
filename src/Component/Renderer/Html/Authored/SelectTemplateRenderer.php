<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\TemplatedViewModelInterface;

use Red\Model\Entity\PaperInterface;

use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class SelectTemplateRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var TemplatedViewModelInterface $viewModel */
        $templatedContent = $viewModel->offsetExists('templatedContent') ? $viewModel->offsetGet('templatedContent') : '';
            $contentTemplate = $viewModel->getContentTemplateName();
        if ($viewModel->isEditableByUser() AND (!isset($contentTemplate) OR !$contentTemplate)) {
            $contentId = $viewModel->getContentId();
            $contentType = $viewModel->getContentType();
            return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/$contentType/$contentId/template"],
                        Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$contentId", "value"=>$contentTemplate])
                        .
                        Html::tag('div', ['class'=>'paper_template_select'],
                                ''
//                            Html::tag('div', ['class'=>'mceNonEditable'], $templatedContent)
                        )
                    );
        } else {
            return Html::tag('div', ['class'=>''], $templatedContent);
        }
    }


//    public function render(iterable $viewModel=NULL) {
//        $templatedContent = $viewModel->offsetExists('templatedContent') ? $viewModel->offsetGet('templatedContent') : '';
//        /** @var PaperViewModelInterface $viewModel */
//        if ($viewModel->isEditableByUser()) {
//            $selectTemplate = $this->renderPaperTemplateButtonsForm($paperAggregate);
////            $selectTemplate = isset($buttons) ? $buttons->renderPaperTemplateButtonsForm($paperAggregate) : "";
////            $paperButton = isset($buttons) ? $buttons->renderPaperButtonsForm($paperAggregate) : "";
//            // atribut data-componentinfo je jen pro info v html
//            return Html::tag('div', ['class'=>$this->classMapEditable->getClass('Segment', 'div')],
//                Html::tag('div', ['class'=>$this->classMapEditable->getClass('Segment', 'div.paper')], $selectTemplate.$templatedContent
//
//                )
//            );
//        } else {
//            return  Html::tag('div', ['class'=>$this->classMapEditable->getClass('Segment', 'div')], $templatedContent);
//        }
//    }

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
