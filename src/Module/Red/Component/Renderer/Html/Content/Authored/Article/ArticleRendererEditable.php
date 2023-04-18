<?php
namespace Red\Component\Renderer\Html\Content\Authored\Article;

use Red\Component\Renderer\Html\Content\Authored\AuthoredRendererAbstract;

use Red\Model\Entity\ArticleInterface;

use Red\Component\View\Content\Authored\AuthoredComponentAbstract;

use Red\Component\ViewModel\Content\Authored\AuthoredViewModelInterface;
use Red\Component\ViewModel\Content\Authored\Article\ArticleViewModelInterface;
use Red\Component\View\Content\Authored\Article\ArticleComponent;

use Red\Middleware\Redactor\Controler\ArticleControler;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class ArticleRendererEditable extends AuthoredRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var ArticleViewModelInterface $viewModel */
        $article = $viewModel->getArticle();  // vrací PaperAggregate

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateArticle')],
                    Html::tag('article', ['data-red-renderer'=>'ArticleRendererEditable', "data-red-datasource"=> "article {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
//                            $this->renderSelectTemplate($viewModel),
                            $viewModel->getContextVariable(ArticleComponent::SELECT_TEMPLATE) ?? '',
                            $this->renderRibbon($viewModel),
                            $this->getFormWithContent($viewModel),

                        ]
                    )
                );
        return $html ?? '';
    }

##### article
    private function getFormWithContent(ArticleViewModelInterface $viewModel) {
        $id = $viewModel->getAuthoredContentId();
        $componentUid = $viewModel->getComponentUid();
        $templateName = $viewModel->getAuthoredTemplateName();

        return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/$id"],
                Html::tag('div',
                    [
                        'id'=> ArticleControler::ARTICLE_CONTENT.$id."_".$componentUid,           // POZOR - id musí být unikátní - jinak selhává tiny selektor
                        'class'=>'edit-html',
                        "data-templatename"=>$templateName,   // toto je selektor pro template css - nastaveno v base-template.less souboru
                    ],
                     $viewModel->getArticle()->getContent()  // co je editovatelné je dáno šablonou
                )
            );
    }


####################################


    protected function renderEditControlButtons(AuthoredViewModelInterface $viewModel): string {
        /** @var ArticleViewModelInterface $viewModel */
        $useSelectTemplateButton = $viewModel->getContextVariable(ArticleComponent::SELECT_TEMPLATE) ? true : false;
        $templateName = $viewModel->getAuthoredTemplateName() ?? '';
        $onclick = (string) "toggleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)
        $buttons = [];
        $disabled = $useSelectTemplateButton ? '' : 'disabled';
        $tooltip = $useSelectTemplateButton ? 'Vybrat šablonu stránky' : 'Nelze podruhé vybrat šablonu stránky';
        $buttons[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', $disabled ? 'button.disabled':'button'),
                'data-tooltip'=> $tooltip,
                'data-position'=>'top right',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"",
                'onclick'=> $disabled ? 'event.preventDefault()' : $onclick
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.template')])
            );
        return $this->renderButtonsDiv($buttons);
    }
}