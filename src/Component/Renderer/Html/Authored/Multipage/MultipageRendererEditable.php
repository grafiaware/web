<?php
namespace Component\Renderer\Html\Authored\Multipage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Red\Model\Entity\MultipageInterface;
use Component\View\Authored\Multipage\MultipageComponent;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class MultipageRendererEditable  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var MultipageViewModelInterface $viewModel */
        $multipage = $viewModel->getMultipage();
        $active = $viewModel->isMenuItemActive();
        $buttonEditContent = (string) $viewModel->getContextVariable(MultipageComponent::CONTEXT_BUTTON_EDIT_CONTENT) ?? '';

        $selectTemplate = $this->renderSelectTemplate($multipage);
        $inner = (string) $viewModel->getContextVariable(MultipageComponent::CONTEXT_TEMPLATE) ?? '';
        $html =
                Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.templateMultipage')],
                  Html::tag('div', ['data-red-renderer'=>'MultipageRendererEditable', "data-red-datasource"=> "multipage {$multipage->getId()} for item {$multipage->getMenuItemIdFk()}"],
                    [
                        $buttonEditContent,
                        $selectTemplate,
                        Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.ribbon-disabled')]), //lepítko s buttony
                        Html::tag('div', ['class'=>$this->classMap->getClass('Content', 'div.semafor')], //aktivní/neaktivní paper
                            Html::tag('div',
                               [
                               'class'=> 'ikona-popis',
                               'data-tooltip'=> $active ? "published" : "not published",
                               ],
                                Html::tag('i',
                                   [
                                   'class'=> $this->classMap->resolveClass($active, 'Content','i1.published', 'i1.notpublished'),
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

    private function renderSelectTemplate(MultipageInterface $multipage) {
        $contentTemplateName = $multipage->getTemplate();
        $multipageId = $multipage->getId();

        return
                    Html::tag('div', ['class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.selectTemplate')],
                        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/multipage/$multipageId/template"],
                            Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$multipageId", "value"=>$contentTemplateName])
                            .
                            Html::tag('div', ['id'=>"multipage_$multipageId", 'class'=>$this->classMap->getClass('PaperTemplateSelect', 'div.tinySelectTemplateMultipage')],'')
                        )

                    );
//                );
    }

}