<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\CompositeView;

use Configuration\ComponentConfigurationInterface;

/**
 * Description of CompositeComponentAbstract
 *
 * @author pes2704
 */
abstract class ComponentAbstract extends CompositeView {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;

    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Vytvoří nový CompositeView s rendererem zadaným jako parametr.
     * Vytvořenému COmponentView jako data nastaví contextData komponenty, jako renderer kontener mu nastaví renderer kontejner komponenty.
     *
     * @param type $rendererClassname
     * @return CompositeView
     */
    protected function createCompositeViewWithRenderer($rendererClassname) {
        // pokud render používá classMap musí být konfigurován v Renderer kontejneru - tam dostane classMap
        return (new CompositeView())->setData($this->contextData)->setRendererName($rendererClassname)->setRendererContainer($this->rendererContainer);
    }

    /**
     * Vytvoří nový CompositeView a šablonou zadanou jako parametr.
     * Vytvořenému COmponentView jako data nastaví contextData komponenty, jako renderer kontener mu nastaví renderer kontejner komponenty.
     *
     * @param PhpTemplateInterface $template
     * @return CompositeView
     */
    protected function createCompositeViewWithTemplate(TemplateInterface $template) {
        return (new CompositeView())->setData($this->contextData)->setTemplate($template)->setRendererContainer($this->rendererContainer);
    }
}
