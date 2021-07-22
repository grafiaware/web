<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\SelectPaperTemplate;

use Component\View\Authored\AuthoredComponentInterface;

/**
 *
 * @author pes2704
 */
interface SelectedPaperTemplateComponentInterface extends AuthoredComponentInterface {
    public function setPaperTemplateName($name): void;
}
