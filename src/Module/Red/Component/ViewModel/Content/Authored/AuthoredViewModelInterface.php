<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\Authored;

use Red\Component\ViewModel\Content\MenuItemViewModelInterface;

use Red\Model\Entity\ItemActionInterface;

/**
 *
 * @author pes2704
 */
interface AuthoredViewModelInterface extends MenuItemViewModelInterface {

    /**
     * Vrací typ položky. MUSÍ použít hodnoty z AuthoredTypeEnum.
     *
     * @return string
     */
    public function getAuthoredContentType(): string;

    /**
     * Vrací jméno template.
     * @return string|null
     */
    public function getAuthoredTemplateName(): ?string;

    /**
     * Vrací id entity, kretá tvoří zobrazovaný obsah - např article, paper, multipge.
     * Spolu s hodnotou type slouží ke generování url pro renderované ovládací prvky
     */
    public function getAuthoredContentId(): string;

    public function getAuthoredContentAction(): ?ItemActionInterface;
    public function userPerformAuthoredContentAction(): bool;
    /**
     * Vrací jméno, které musí být v rendereru použito jako id pro element, na kterém visí tiny editor.
     * POZOR - id musí být unikátní - jinak selhává tiny selektor - a "nic není vidět"
     *
     * @return string
     */
    public function getTemplateContentPostVarName();
}
