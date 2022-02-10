<?php
namespace Component\Renderer\Html\Manage;

use Component\Renderer\Html\HtmlRendererAbstract;

use Pes\Text\Html;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

/**
 * Description of ToggleEditButtonRenderer
 *
 * @author pes2704
 */
class SelectTemplateRenderer extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var AuthoredViewModelInterface $viewModel */
        $contentTemplateName = $viewModel->getAuthoredTemplateName();
        $authoredContentId = $viewModel->getAuthoredContentId();

        $type = $viewModel->getItemType();
        // $templateContentPostVar použito jako id pro element, na které visí tiny - POZOR - id musí být unikátní - jinak selhává tiny selektor - a "nic není vidět"
        switch ($type) {
            case AuthoredTypeEnum::ARTICLE:
                $templateContentPostVar = AuthoredControlerAbstract::ARTICLE_TEMPLATE_CONTENT.$authoredContentId;
                break;
            case AuthoredTypeEnum::PAPER:
                $templateContentPostVar = AuthoredControlerAbstract::PAPER_TEMPLATE_CONTENT.$authoredContentId;
                break;
            case AuthoredTypeEnum::MULTIPAGE:
                $templateContentPostVar = AuthoredControlerAbstract::MULTIPAGE_TEMPLATE_CONTENT.$authoredContentId;
                break;
            default:
                throw new UnexpectedValueException("Neznámý typ item '$type'. Použijte příkaz 'Zpět' a nepoužívejte tento typ obsahu.");
        }

        return
            // id je parametr pro togleTemplateSelect(id) - voláno onclick na button 'Vybrat šablonu stránky'
            Html::tag('div', ['id'=> $this->getTemplateSelectId($viewModel),'class'=>$this->classMap->get('Template', 'div.selectTemplate')],
                Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/$type/$authoredContentId/template"],
                    [
//                        Html::tagNopair('input', ["type"=>"hidden", "name"=>$templateNamePostVar, "value"=>$contentTemplateName]),
//
                        // class je třída pro selector v tinyInit var selectTemplateConfig
//                        Html::tag('div', ['id'=>$templateContentPostVar, 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplateArticle')],''),
                        Html::tag('div', ['id'=>$templateContentPostVar, 'class'=>"tiny_select_template_$type"],''),       // POZOR - id musí být unikátní - jinak selhává tiny selektor
                    ]
                )
            );
    }

    protected function getTemplateSelectId(AuthoredViewModelInterface $viewModel) {
        $type = $viewModel->getItemType();
        $articleId = $viewModel->getAuthoredContentId(); //$article->getId();
        return "select_template_{$type}_{$articleId}";
    }

}
