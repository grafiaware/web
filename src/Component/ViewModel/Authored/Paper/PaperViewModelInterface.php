<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Model\Entity\MenuNodeInterface;
use Model\Entity\PaperInterface;

/**
 *
 * @author pes2704
 */
interface PaperViewModelInterface extends AuthoredViewModelInterface {
    /**
     * Vrací položku menu.
     *
     * @return MenuNodeInterface
     */
    public function getMenuNode();

    /**
     * Vrací paper.
     *
     * @return PaperInterface
     */
    public function getPaper();}
