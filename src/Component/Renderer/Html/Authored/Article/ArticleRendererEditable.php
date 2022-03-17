<?php
namespace Component\Renderer\Html\Authored\Article;

use Component\Renderer\Html\Authored\AuthoredRendererAbstract;

use Red\Model\Entity\ArticleInterface;

use Component\View\MenuItem\Authored\AuthoredComponentAbstract;

use Component\ViewModel\MenuItem\Authored\AuthoredViewModelInterface;
use Component\ViewModel\MenuItem\Authored\Article\ArticleViewModelInterface;
use Component\View\MenuItem\Authored\Article\ArticleComponent;

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
        $templateName = $viewModel->getAuthoredTemplateName();

        return Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/article/$id"],
                Html::tag('div',
                    [
                        'id'=> ArticleControler::ARTICLE_CONTENT.$id,           // POZOR - id musí být unikátní - jinak selhává tiny selektor
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
        $templateName = $viewModel->getAuthoredTemplateName() ?? '';
        $onclick = (string) "togleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)
        $buttons = [];
        $disabled = $templateName ? 'disabled' : '';
        $tooltip = $templateName ? 'Nelze podruhé vybrat šablonu stránky' : 'Vybrat šablonu stránky';
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