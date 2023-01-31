<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Web\Component\ViewModel\Menu;

use Web\Component\ViewModel\ViewModelAbstract;
use Web\Component\ViewModel\Menu\LevelViewModelInterface;

use LogicException;

/**
 * Description of LevelViewModel
 *
 * @author pes2704
 */
class LevelViewModel extends ViewModelAbstract implements LevelViewModelInterface {

    private $isLastLevel=false;

    public function setLastLevel($level) {
        $this->isLastLevel = $level;
    }

    public function isLastLevel() {
        return $this->isLastLevel;
    }
}
