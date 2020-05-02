<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\View;
use Pes\View\Renderer\RendererInterface;

use Component\ViewModel\ViewModelInterface;

/**
 * Description of ComponentAbstract
 *
 * @author pes2704
 */
abstract class ComponentAbstract extends View {
    /**
     *
     * @var ViewModelInterface
     */
    protected $viewModel;

    /**
     *
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * Vrací string vzniklý renderováním rendererem komponenty a použitím dat view modelu zadaného do konstruktoru komponenty. Pokud
     * je zadán parametr $data metody, použitjí se tato data přednostně, nahradí view model zadaný do konstruktoru komponenty.
     *
     * @param type $data Data typu ViewModel vhodného pro renderování rendererem komponenty
     * @return type
     */
    public function getString($data=null) {
        return parent::getString(isset($data) ? $data : $this->viewModel);  // jakýkoli parametr má přednost před $this->viewModel - i prázdný string
    }
}
