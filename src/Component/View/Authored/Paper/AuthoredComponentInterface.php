<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Paper;

/**
 *
 * @author pes2704
 */
interface AuthoredComponentInterface {
//    public function setEditable($editable);
    public function setPaperTemplatesPath($templatesPath);
    public function addTemplateGlobals($variableName, $rendererName);
}