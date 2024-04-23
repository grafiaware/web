<?php
namespace  Red\Component\Renderer\Html\Menu;

use Component\Renderer\Html\HtmlRendererAbstract;
use Red\Component\ViewModel\Menu\DriverViewModelInterface;
use Red\Component\View\Menu\DriverComponentInterface;

use Pes\Text\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DriverRendererEditable
 *
 * @author pes2704
 */
class DriverRendererEditable extends HtmlRendererAbstract {

    /**
     *
     * @var DriverViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderEditableItem($viewModel);
    }

    protected function renderEditableItem(DriverViewModelInterface $viewModel) {
        $itemHtml = Html::tag('a',
            [
                'class'=>[
                    $this->classMap->get('Item', 'li a'),
                    $this->classMap->get('Item', 'li'),
                    ],
                'href'=>$viewModel->getPageApi(),
            ]
            +$this->dataRedAttributes($viewModel),
            Html::tag('span', ['class'=>$this->classMap->get('Item', 'li a span')],
                $viewModel->getTitle()
            )
            . $this->semafor($viewModel)
        );

        return $itemHtml;
    }
    
    private function dataRedAttributes(DriverViewModelInterface $viewModel) {
        return [
            'data-red-content'=>$viewModel->getRedContentApi(),
            'data-red-driver'=>$viewModel->getRedDriverApi(),            
        ];
    }

    private function semafor(DriverViewModelInterface $viewModel) {
        $active = $viewModel->isActive();
        $html = Html::tag('span', ['class'=>$this->classMap->get('Item', 'semafor')],
                    Html::tag('i', [
                        'class'=> $this->classMap->resolve($active, 'Icons', 'semafor.published', 'semafor.notpublished'),
                        'title'=> $active ? "published" :  "not published"
                        ])
                );
        return $html;
    }
}
