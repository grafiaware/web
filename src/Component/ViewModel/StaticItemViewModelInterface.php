<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

use Psr\Container\ContainerInterface;

use Red\Model\Entity\StaticItemInterface;

/**
 *
 * @author pes2704
 */
interface StaticItemViewModelInterface extends ViewModelInterface {

    public function injectContainer(ContainerInterface $container): StaticItemViewModelInterface;
    
    public function getStaticItemId(): string;
    public function getStaticItemPath(): string;
    public function getStaticItemTemplate(): string;
    public function isEditable(): bool;
    public function getStaticFullTemplatePath(): string;

    /**
     * Seznam šablon z remote auth/events (pro select box v editoru).
     * Prázdné pole = renderer použije textová input pole.
     *
     * @return array<int, array{path: string, template: string, fullTemplatePath?: string}>
     */
    public function getTemplateOptions(): array;
}
