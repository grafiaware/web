<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

/**
 *
 * @author pes2704
 */
interface StatusViewModelInterface extends ViewModelInterface {

    public function getFlashCommand($key);
    public function getPostFlashCommand($key);
    public function getUserRole(): ?string;
    public function getUserLoginName(): ?string;

    /**
     * Prezentuj článek v editovatelné podobě
     * @return bool
     */
    public function isArticleEditable(): bool;
    public function isLayoutEditable(): bool;
    public function isMenuEditable(): bool;
}
