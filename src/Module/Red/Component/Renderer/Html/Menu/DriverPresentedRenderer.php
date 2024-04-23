<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;

use Red\Component\ViewModel\Menu\DriverViewModelInterface;

use Pes\Text\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Svisle
 *
 * @author pes2704
 */
class DriverPresentedRenderer extends HtmlRendererAbstract {

    /**
     *
     * @var DriverViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderNoneditableItem($viewModel);
    }

    private function renderNoneditableItem(DriverViewModelInterface $viewModel) {
        $anchorHtml = Html::tag('a',
                [
                    'class'=>[
                        $this->classMap->get('Item', 'li a'),
                        $this->classMap->get('Item', 'li.presented'),   
                        ],
                    'href'=>$viewModel->getPageApi(),                    
                ]
                +$this->dataRedAttributes($viewModel),                
                Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                    $viewModel->getTitle()
                )
            );

        return $anchorHtml;
    }
    
    private function dataRedAttributes(DriverViewModelInterface $viewModel) {
        return [
            'data-red-content'=>$viewModel->getRedContentApi(),
            'data-red-driver'=>$viewModel->getRedDriverApi(),            
        ];
    }
}
