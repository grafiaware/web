<?php
namespace Component\Renderer\Html\Authored\Multipage;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Multipage\MultipageViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\MultipageInterface;
use Red\Model\Entity\PaperContentInterface;

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
        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';

        $selectTemplate = $this->renderSelectTemplate($multipage);
        $inner = (string) $viewModel->getContextVariable('template') ?? '';
        $html =
                Html::tag('div', ['data-red-renderer'=>'MultipageRendererEditable', "data-red-datasource"=> "multipage {$multipage->getId()} for item {$multipage->getMenuItemIdFk()}"],
                    [
                        $buttonEditContent,
                        $selectTemplate,
                        Html::tag('div',
                            ['class'=>$this->classMap->getClass('Content', 'div.semafor')], //aktivní/neaktivní paper
                            Html::tag('div',
                                ['class'=> 'ikona-popis', 'data-tooltip'=> $active ? "published" : "not published"],
                                Html::tag('i', ['class'=> $this->classMap->resolveClass($active, 'Content','i1.published', 'i1.notpublished')])
                            )
                        ),
                        $inner
                    ]
                );
        return $html ?? '';
    }

    private function renderSelectTemplate(MultipageInterface $multipage) {
        $contentTemplateName = $multipage->getTemplate();
        $multipageId = $multipage->getId();

        return
                    Html::tag('div', ['class'=>$this->classMap->getClass('MultipageTemplateSelect', 'div.selectTemplate')],
                        Html::tag('form', ['method'=>'POST', 'action'=>"red/v1/multipage/$multipageId/template"],
                            Html::tagNopair('input', ["type"=>"hidden", "name"=>"template_$multipageId", "value"=>$contentTemplateName])
                            .
                            Html::tag('div', ['id'=>"multipage_$multipageId", 'class'=>$this->classMap->getClass('MultipageTemplateSelect', 'div.tinyPaperSelect')],'')
                        )

                    );
//                );
    }

}