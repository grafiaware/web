<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Menu\Middleware\Menu\View\Renderer;

use Pes\View\Renderer\RendererInterface;

/**
 * Description of DetailRenderer
 *
 * @author pes2704
 */
class DetailRenderer implements RendererInterface {
    public function render($data = NULL) {
        return "<div><H4>Toto je detail {$data['id']}</h4></div>";
    }
}
