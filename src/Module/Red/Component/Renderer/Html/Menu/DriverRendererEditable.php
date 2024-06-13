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
        $isPresented = $viewModel->isPresented();
        $isRoot = $viewModel->getMenuItem()->getApiGeneratorFk()=='root';
        if ($isPresented) {
            if ($isRoot) {
                $pAtttributes = [];                
            } else {
                $pAtttributes = [
                        'contenteditable'=> "true", 
                        'data-original-title'=>$viewModel->getTitle(),  // odesílá edit.js v datech POST request pro info
                        'data-red-item-title-uri'=>$viewModel->getRedItemTitleApi()
                        ];
            }
            $pHtml = Html::tag('p', 
                        $pAtttributes,
                        $viewModel->getTitle()
                    );
            
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
                    $this->form($viewModel)
                    .
                    // POZOR: závislost na edit.js
                    // ve skriptu edit.js je element k editaci textu položky vybírán pravidlem (selektorem):
                    //  acceptedElement = targetElement.nodeName === 'P' && targetElement.parentNode.nodeName === 'DIV',
                    // t.j. selektor vybírá <p>, který má rodiče <div>
                    $pHtml
                    .
                    $this->semafor($viewModel) 
                );
        } else {
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
        }

        return $itemHtml;
    }
    
    private function dataRedAttributes(DriverViewModelInterface $viewModel) {
        return [
            'data-red-content'=>$viewModel->getRedContentApi(),
            'data-red-driver'=>$viewModel->getRedDriverApi(),          
            'data-red-presenteddriver'=>$viewModel->getRedPresenterDriverApi()            
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
    
    private function form(DriverViewModelInterface $viewModel) {
        $buttonsHtml = $viewModel->offsetExists(DriverComponentInterface::DRIVER_BUTTONS) ? $viewModel->offsetGet(DriverComponentInterface::DRIVER_BUTTONS) : "";
        return Html::tag('form', 
            ['class'=>'apiAction'],
            Html::tag('div',
                ['class'=>$this->classMap->get('Buttons', 'div.buttons')],
                $buttonsHtml)
        );    
    }
}
