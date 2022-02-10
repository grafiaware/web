<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\Authored\AuthoredRendererAbstract;

use Component\View\Authored\AuthoredComponentAbstract;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

use Pes\Text\Html;

use Component\View\Authored\Paper\PaperComponent;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRendererEditable  extends AuthoredRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $article = $viewModel->getPaper();  // vrací PaperAggregate

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templatePaper')],
                    Html::tag('article', ['data-red-renderer'=>'PaperRendererEditable', "data-red-datasource"=> "paper {$article->getId()} for item {$article->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $this->renderSelectTemplate($viewModel),
                            $this->renderRibbon($viewModel),
                            $viewModel->getContextVariable(PaperComponent::CONTENT) ?? '',   // obsah z papet komponenty
                        ]
                    )
                );
        return $html ?? '';
    }

####################################

    protected function renderContentControlButtons(PaperViewModelInterface $viewModel): array {
        $templateName = $viewModel->getAuthoredTemplateName() ?? '';
        $paper = $viewModel->getPaper();
        $paperId = $paper->getId();
        $onclick = (string) "togleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)
        $buttons = [];

        $buttons[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> 'Vybrat šablonu stránky',
                'data-position'=>'top right',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"",
                'onclick'=>$onclick
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.template')])
            );

        if ($paper instanceof PaperAggregatePaperContentInterface AND $paper->getPaperContentsArray()) {
            $buttons[] = Html::tag('button', [
                    'class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=> 'Seřadit podle data',
                    'data-position'=>'top right',
                    'formmethod'=>'post',
                    'formaction'=>"",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrange')])
                );
        } else {
            $buttons[] =  Html::tag('button',
                    ['class'=>$this->classMap->get('Buttons', 'button'),
                    'data-tooltip'=>'Přidat sekci',
                    'type'=>'submit',
                    'name'=>'button',
                    'value' => '',
                    'formmethod'=>'post',
                    'formaction'=>"red/v1/paper/$paperId/section",
                    ],
                    Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icons')],
                        Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.addcontent')])
                        .Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.arrowdown')])
                    )
                );
        }

        return $buttons;
    }
}