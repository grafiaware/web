<?php
namespace Red\Component\Renderer\Html\Content\Authored\Multipage;

use Red\Component\Renderer\Html\Content\Authored\AuthoredRendererAbstract;

use Red\Component\ViewModel\Content\Authored\AuthoredViewModelInterface;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageViewModelInterface;
use Red\Component\View\Content\Authored\Multipage\MultipageComponent;
use Red\Component\View\Content\Authored\AuthoredComponentAbstract;

use Red\Model\Entity\MultipageInterface;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class MultipageRendererEditable  extends AuthoredRendererAbstract {

    public function render(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $multipage = $viewModel->getMultipage();

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateMultipage')],
                  Html::tag('div', ['data-red-renderer'=>'MultipageRendererEditable', "data-red-datasource"=> "multipage {$multipage->getId()} for item {$multipage->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
//                            $this->renderSelectTemplate($viewModel),
                            $viewModel->getContextVariable(MultipageComponent::SELECT_TEMPLATE) ?? '',
                            $this->renderRibbon($viewModel),
                            $viewModel->getContextVariable(MultipageComponent::CONTENT) ?? '',
                        ]
                    )
                );
        return $html ?? '';
    }

    protected function renderEditControlButtons(AuthoredViewModelInterface $viewModel): string {
        /** @var PaperViewModelInterface $viewModel */
        $authoredId = $viewModel->getAuthoredContentId();
        $onclick = (string) "toggleTemplateSelect(event, '{$this->getTemplateSelectId($viewModel)}');";   // ! chybná syntaxe javascriptu vede k volání form action (s nesmyslným uri)
        $buttons = [];

        $buttons[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> 'Vybrat styl zobrazení stránky',
                'data-position'=>'bottom center',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"",
                'onclick'=>$onclick
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.template')])
            );
        $buttons[] = Html::tag('button', [
                'class'=>$this->classMap->get('Buttons', 'button'),
                'data-tooltip'=> 'Odstranit styl zobrazení stránky',
                'data-position'=>'bottom center',
                'formtarget'=>'_self',
                'formmethod'=>'post',
                'formaction'=>"red/v1/multipage/$authoredId/templateremove",
                ],
                Html::tag('i', ['class'=>$this->classMap->get('Icons', 'icon.templateremove')])
            );

        return $this->renderButtonsDiv($buttons);
    }


}