<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mail\MessageFactory;

use Pes\View\ViewFactory;
use Pes\View\Renderer\PhpTemplateRenderer;

/**
 * Description of Html
 *
 * @author pes2704
 */
class HtmlMessage {
    public function create($templateFilePath, $context) {
        return (string) (new ViewFactory())->phpTemplateView($templateFilePath, $context)->setRenderer(new PhpTemplateRenderer());
    }

}
