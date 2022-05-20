<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Component\ViewModel\Manage;

use Component\ViewModel\StatusViewModel;

/**
 * Description of ToggleEditMenuViewModel
 *
 * @author pes2704
 */
class EditMenuSwitchViewModel extends StatusViewModel implements ToggleEditMenuViewModelInterface {
    public function getIterator() {
        $this->appendData([
            'editMenu' => $this->presentEditableMenu(),
        ]);
        return parent::getIterator();
    }

}
