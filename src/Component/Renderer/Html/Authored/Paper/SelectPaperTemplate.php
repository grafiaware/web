<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored\Paper;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class SelectPaperTemplate {

    public function renderSelectPaperTemplate(PaperAggregatePaperContentInterface $paper) {
        $contentTemplateName = $paper->getTemplate();
            $paperId = $paper->getId();
            return
            Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/paper/$paperId/template"],
                Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$paperId", "value"=>$contentTemplateName])
                .
                Html::tag('div', ['class'=>'paper_template_select'],''
                )
            );
    }
}
