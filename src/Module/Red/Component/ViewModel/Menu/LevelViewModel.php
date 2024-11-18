<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Red\Component\ViewModel\Menu;

use Component\ViewModel\ViewModelAbstract;
use Red\Component\ViewModel\Menu\LevelViewModelInterface;

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
