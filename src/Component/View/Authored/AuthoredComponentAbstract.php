<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored;

use Component\View\StatusComponentAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Pes\View\Template\TemplateInterface;
use Pes\View\Template\Exception\NoTemplateFileException;
use Pes\View\CompositeView;

/**
 * Description of AuthoredComponentAbstract
 * Objekt je potomkem CompositeView.
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends StatusComponentAbstract implements AuthoredComponentInterface {

    const DEFAULT_TEMPLATE_FILE_NAME = 'template.php';
    const DEFAULT_TEMPLATE_NAME = 'default';

    /**
     * @var AuthoredViewModelInterface
     */
    protected $contextData;

    public function setItemId($menuItemId): AuthoredComponentInterface {
        $this->contextData->setItemId($menuItemId);
        return $this;
    }

    public function getTemplateFileFullname($templatesPath, $templateName): string {
        return $templatesPath.$templateName."/".self::DEFAULT_TEMPLATE_FILE_NAME;
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
