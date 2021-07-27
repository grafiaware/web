<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored;

use Pes\View\CompositeViewInterface;

/**
 *
 * @author pes2704
 */
interface AuthoredComponentInterface extends CompositeViewInterface {

    public function setItemId($menuItemId): AuthoredComponentInterface;

    public function getTemplateFileFullname($templatesPath, $templateName): string;
}
