<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\MenuItem\Authored\PaperTemplate;

use Component\View\MenuItem\Authored\AuthoredComponentInterface;

/**
 *
 * @author pes2704
 */
interface MultipageTemplateComponentInterface extends AuthoredComponentInterface {
    public function setSelectedPaperTemplateFileName($name): void;
}
