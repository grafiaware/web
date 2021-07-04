<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

/**
 *
 * @author pes2704
 */
interface TemplatedViewModelInterface extends AuthoredViewModelInterface {
    /**
     * Jméno template získané z entity - jméno složky s template soubory pro template renderer.
     */
    public function getContentTemplateName();

}
