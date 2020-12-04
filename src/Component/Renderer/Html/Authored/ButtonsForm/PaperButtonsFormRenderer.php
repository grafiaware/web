<?php
namespace Component\Renderer\Html\Authored\ButtonsForm;

use Component\Renderer\Html\HtmlRendererAbstract;
use Pes\Text\Html;

use Model\Entity\PaperAggregateInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PaperButtonsFormRenderer
 *
 * @author pes2704
 */
class PaperButtonsFormRenderer extends HtmlRendererAbstract {
    public function render($data = NULL) {
        return $this->renderPrivate($data);
    }

    private function renderPrivate(PaperAggregateInterface $paperAggregate) {
        $paperId = $paperAggregate->getId();
        return
            Html::tag('form', ['method'=>'POST', 'action'=>""],
                Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateButtons', 'div.paperTemplate')],
                    Html::tag('button', [
                        'class'=>$this->classMap->getClass('PaperTemplateButtons', 'button'),
                        'data-tooltip'=>'Výběr šablony stránky',
                        'data-position'=>'top right',
                        'type'=>'submit',
                        'name'=>'',
                        'formmethod'=>'post',
                        'formaction'=>"api/v1/paper/$paperId/template/",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->getClass('PaperTemplateButtons', 'button.templateSelect')])
                    )
                )
            );
    }
}
