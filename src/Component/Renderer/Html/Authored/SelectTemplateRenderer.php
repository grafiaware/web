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
        $article = $viewModel->getArticle();

        $contentTemplateName = $viewModel->getItemTemplate();
        $itemType = $viewModel->getItemType();
        $itemId = $viewModel->getItemId();
        $selectTemplateElementId = "select_template_$itemType_$itemId";

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
                    Html::tag('i', ['class'=>$this->classMap->get('PaperTemplateSelect', 'div i')])
                )
            )
            .
            Html::tag('div', ['id'=>$selectTemplateElementId,'class'=>$this->classMap->get('PaperTemplateSelect', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/$itemId/template"],
//                    Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$articleId", "value"=>$contentTemplateName])
//                    .
                    // class tohoto divu je třída pro selector v tinyInit var selectTemplateConfig
                    // položka classmapy 'div.tinySelectTemplateArticle' vede na class, např. tiny_select_template_paper (v ConfigurationStyles)
                    // class tiny_select_template_paper je selektor pro TinyInit.js (v public) - vybere konfiguraci tiny a v té je proměnná templates se seznamem šablon
                    // např. templates: templates_article (jiný seznam pro paper, article, multipage),
                    // proměnné templates_article a další jsou pak definovány v local šablonách tinyConfig.js
                        //TODO: Sv
                    Html::tag('div', ['id'=>"template_$itemId", 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplateArticle')],'')
                )

            )
        );

        // kopie z PaperRendererEditable->renderSelectTemplate()

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
}
