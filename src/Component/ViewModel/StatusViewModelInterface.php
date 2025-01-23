<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Red\Model\Entity\LanguageInterface;
use Red\Model\Entity\EditorActionsInterface;
use Events\Model\Entity\RepresentationActionsInterface;

use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface StatusViewModelInterface extends ViewModelInterface {

    public function getFlashCommand($key);
    public function getFlashPostCommand($key);
    public function getFlashMessages();

    public function getUserRole(): ?string;
    public function getUserLoginName(): ?string;
    public function getUserLoginHash(): ?string;
    public function getUserEmail(): ?string;
    public function isUserLoggedIn(): bool;
    /**
     * Prezentuj generovaný obsah v editovatelné podobě
     * @return bool
     */
    public function presentEditableContent(): bool;
    public function getPresentedLanguage(): ?LanguageInterface;
    public function getPresentedMenuItem(): ?MenuItemInterface;

    public function getEditorActions(): ?EditorActionsInterface;
    public function getRepresentativeActions(): ?RepresentationActionsInterface;
    // pro InfoBoard
    public function getSecurityInfos(): array;
    public function getPresentationInfos(): array;
            
}
