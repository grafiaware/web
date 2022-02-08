<?php
namespace Component\Renderer\Html\Authored\Multipage;

use Component\Renderer\Html\Authored\AuthoredRendererAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Red\Model\Entity\MultipageInterface;
use Component\View\Authored\Multipage\MultipageComponent;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class MultipageRendererEditable  extends AuthoredRendererAbstract {
    public function renderOLD(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $multipage = $viewModel->getMultipage();
        $menuItem = $viewModel->getMenuItem();
        $buttonEditContent = (string) $viewModel->getContextVariable(MultipageComponent::CONTEXT_BUTTON_EDIT_CONTENT) ?? '';
        $selectTemplate = $this->renderSelectTemplate($multipage);
        $inner = (string) $viewModel->getContextVariable(MultipageComponent::CONTENT) ?? '';
        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateMultipage')],
                  Html::tag('div', ['data-red-renderer'=>'MultipageRendererEditable', "data-red-datasource"=> "multipage {$multipage->getId()} for item {$multipage->getMenuItemIdFk()}"],
                    [
                        $buttonEditContent,
                        $selectTemplate,
                        Html::tag('div', ['class'=>$this->classMap->get('Buttons', 'div.ribbon-disabled')]), //lepítko s buttony
                        Html::tag('div', ['class'=>$this->classMap->get('Content', 'div.semafor')], //aktivní/neaktivní paper
                            Html::tag('div',
                               [
                               'class'=> 'ikona-popis',
                               'data-tooltip'=> $menuItem->getActive() ? "published" : "not published",
                               ],
                                Html::tag('i',
                                   [
                                   'class'=> $this->classMap->resolve($menuItem->getActive(), 'Icons','semafor.published', 'semafor.notpublished'),
                                   ]
                                )
                            )
                        ),
                        $inner
                    ]
                )
              );
        return $html ?? '';
    }

    #############################
    public function render(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $multipage = $viewModel->getMultipage();

        $html =
                Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.templateMultipage')],
                  Html::tag('div', ['data-red-renderer'=>'MultipageRendererEditable', "data-red-datasource"=> "multipage {$multipage->getId()} for item {$multipage->getMenuItemIdFk()}"],
                        [
                            $viewModel->getContextVariable(AuthoredComponentAbstract::BUTTON_EDIT_CONTENT) ?? '',
                            $this->renderSelectTemplate($viewModel),
                            $this->renderRibbon($viewModel),
                            $viewModel->getContextVariable(MultipageComponent::CONTENT) ?? '',
                        ]
                    )
                );
        return $html ?? '';
    }

    private function renderSelectTemplate(MultipageInterface $multipage) {
        $contentTemplateName = $multipage->getTemplate();
        $multipageId = $multipage->getId();

        return
                    Html::tag('div', ['class'=>$this->classMap->get('Template', 'div.selectTemplate')],
                        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/multipage/$multipageId/template"],
                            Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$multipageId", "value"=>$contentTemplateName])
                            .
                            Html::tag('div', ['id'=>"multipage_$multipageId", 'class'=>$this->classMap->get('PaperTemplateSelect', 'div.tinySelectTemplateMultipage')],'')
                        )

                    );
//                );
    }

}