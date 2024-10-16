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
class DriverPresentedRendererEditable extends HtmlRendererAbstract {

    /**
     *
     * @var DriverViewModelInterface
     */
    protected $viewModel;

    public function render(iterable $viewModel=NULL) {
        return $this->renderEditableItem($viewModel);
    }

    protected function renderEditableItem(DriverViewModelInterface $viewModel) {
        $buttonsHtml = $viewModel->offsetExists(DriverComponentInterface::DRIVER_BUTTONS) ? $viewModel->offsetGet(DriverComponentInterface::DRIVER_BUTTONS) : "";

        $itemHtml =  // W3 specifications, <p> is only allowed to contain text or 'inline' (not 'block') tags
            Html::tag('div',
                [
                'class'=>[
                    $this->classMap->get('Item', 'li a'),   // class - 'editable' v kontejneru
                    $this->classMap->get('Item', 'li.presented'),
                    ],
                'tabindex'=>0,
                ]
                +$this->dataRedAttributes($viewModel),

                // POZOR: závislost na edit.js
                // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem) acceptedElement = targetElement.nodeName === 'SPAN' && targetElement.parentNode.nodeName === 'P',
                // t.j. selektor vybírá <span>, který má rodiče <p>
                Html::tag('p', ['contenteditable'=> "true", 'data-original-title'=>$viewModel->getTitle()],
                    $viewModel->getTitle()
                )
                .
                $this->semafor($viewModel) 
                .
                Html::tag('form', 
                    [
                    ],
                    Html::tag('div',
                        ['class'=>$this->classMap->get('Buttons', 'div.buttons')],
                        $buttonsHtml)
                )
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
