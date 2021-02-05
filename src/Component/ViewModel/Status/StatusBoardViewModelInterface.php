<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Status;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface StatusBoardViewModelInterface extends ViewModelInterface {
    public function getLanguageInfo();
    public function getEditableInfo();
    public function getSecurityInfo();
}
