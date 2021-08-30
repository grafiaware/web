<?php
namespace Component\Renderer\Html\Authored\Paper;

use Component\Renderer\Html\HtmlRendererAbstract;
use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperInterface;
use Red\Model\Entity\PaperContentInterface;

use Component\Renderer\Html\Authored\Paper\HeadlineRenderer;

use Pes\Text\Html;

/**
 * Description of PaperRenderer
 *
 * @author pes2704
 */
class PaperRenderer  extends HtmlRendererAbstract {
    public function render(iterable $viewModel=NULL) {
        /** @var PaperViewModelInterface $viewModel */
        $paperAggregate = $viewModel->getPaper();  // vrací PaperAggregate
//        $setEditableButton =
//                $viewModel->presentEditableArticle() ?
//                Html::tag('div', ['class'=>$this->classMap->getClass('PaperButtons', 'div.editMode')], //tlačítko "tužka" pro zvolení editace
//                    Html::tag('form', ['method'=>'POST', 'action'=>''],
//                        Html::tag('button', [
//                            'class'=>$this->classMap->getClass('PaperButtons', 'div.editMode button'),
//                            'data-tooltip' => 'Článek můžete editovat',
//                            'name' => 'edit_article',
//                            'value' => '',
//                            'type' => 'submit',
//                            'formtarget' => '_self',
//                            ],
//                            Html::tag('i', ['class'=>$this->classMap->getClass('PaperButtons', 'div.editMode i')])
//                        )
//                    )
//            )
//            :
//            ''
//        ;
        $inner = (string) $viewModel->getContextVariable('template') ?? '';
        $buttonEditContent = (string) $viewModel->getContextVariable('buttonEditContent') ?? '';
        $html =
                Html::tag('article', ['data-red-renderer'=>'PaperRenderer', "data-red-datasource"=> "paper {$paperAggregate->getId()} for item {$paperAggregate->getMenuItemIdFk()}"],
                        [$buttonEditContent, $inner]
                );
        return $html ?? '';
    }
}