<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\Renderer\Html\Authored;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Red\Model\Entity\ArticleInterface;

use Pes\Text\Html;
/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class SelectTemplateRenderer extends HtmlRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        return
                $this->renderSelectTemplate($viewModel)
                .
                $viewModel->getArticle()->getContent()
                ;
    }

    private function renderSelectTemplate(AuthoredViewModelInterface $viewModel) {
        $contentTemplateName = $viewModel->getAuthoredTemplateName();
        $itemType = $viewModel->getItemType();
        $contentId = $viewModel->getAuthoredContentId();

        $urlId = "{$itemType}_{$contentId}";
        $selectTemplateElementId = "select_template_$urlId";

        return
        Html::tag('div', [],
            Html::tag('button', [
                'class'=>$this->classMap->get('PaperTemplateSelect', 'div button'),
                'formtarget'=>'_self',
                'tabindex'=>'0',
                'onclick'=>"togleTemplateSelect(event, '$selectTemplateElementId'); "
                ],
                Html::tag('div', ['class'=>$this->classMap->get('PaperTemplateSelect', 'div.hidden')], 'Šablony pro stránku')
                .Html::tag('div', ['class'=>$this->classMap->get('PaperTemplateSelect', 'div.visible')],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.template')])
                )
            )
            .
            Html::tag('div', ['id'=>$selectTemplateElementId,'class'=>$this->classMap->get('Template', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/$itemType/$contentId/template"],
                    Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$contentId", "value"=>$contentTemplateName])
                    .
                    // class tohoto divu je třída pro selector v tinyInit var selectTemplateConfig
                    // položka classmapy 'div.tinySelectTemplateArticle' vede na class, např. tiny_select_template_paper (v ConfigurationStyles)
                    // class tiny_select_template_article je selektor pro TinyInit.js (v public) - vybere konfiguraci tiny a v té je proměnná templates se seznamem šablon
                    // např. templates: templates_article (jiný seznam pro paper, article, multipage) - teď se seznamy načítací z TemplateCtrl
                        //TODO: Sv
                    Html::tag('div', ['id'=>"$urlId", 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplateArticle')],'')
                 )

            )
        );

        // kopie z PaperRendererEditable->renderSelectTemplate()

    }
}
