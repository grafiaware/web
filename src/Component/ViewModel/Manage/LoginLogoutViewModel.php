<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Component\ViewModel\Manage;

use Component\ViewModel\StatusViewModel;
/**
 * Description of LoginLogoutViewModel
 *
 * @author pes2704
 */
class LoginLogoutViewModel extends StatusViewModel implements LoginLogoutViewModelInterface {
    public function getIterator() {
        $this->appendData([
                        'userName' => $this->getUserLoginName()
        ]);
        return parent::getIterator();
    }
}
