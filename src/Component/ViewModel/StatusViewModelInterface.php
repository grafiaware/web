<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Red\Model\Entity\UserActionsInterface;

/**
 *
 * @author pes2704
 */
interface StatusViewModelInterface extends ViewModelInterface {

    public function getFlashCommand($key);
    public function getPostFlashCommand($key);

    public function isUserLoggedIn(): bool;
    public function getUserRole(): ?string;
    public function getUserLoginName(): ?string;

    /**
     * Prezentuj generovaný obsah v editovatelné podobě
     * @return bool
     */
    public function presentEditableContent(): bool;
    public function presentEditableLayout(): bool;
    public function presentEditableMenu(): bool;

    public function getUserActions(): ?UserActionsInterface;
}
