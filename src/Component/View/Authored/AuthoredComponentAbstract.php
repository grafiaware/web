<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Pes\View\Template\PhpTemplateInterface;
use Pes\View\Template\Exception\NoTemplateFileException;

/**
 * Description of AuthoredComponentAbstract
 * Objekt je potomkem CompositeView.
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends CompositeComponentAbstract implements AuthoredComponentInterface {

    const DEFAULT_TEMPLATE_FILE_NAME = 'template.php';
    const DEFAULT_TEMPLATE_NAME = 'default';

    /**
     * @var string
     */
    protected $templatesPath;

    protected $childRenderers = [];

    /**
     * @var AuthoredViewModelInterface
     */
    protected $contextData;

    /**
     * {@inheritdoc}
     */
    public function addChildRendererName($variableName, $rendererName) {
        $this->childRenderers[$variableName] = $rendererName;
    }

    public function setItemId($menuItemId): AuthoredComponentInterface {
        $this->contextData->setItemId($menuItemId);
        return $this;
    }

    public function getTemplateFileFullname($templatesPath, $templateName): string {
        return $templatesPath.$templateName."/".self::DEFAULT_TEMPLATE_FILE_NAME;
    }

    /**
     *
     * @param PhpTemplateInterface $template
     * @return void
     */
    protected function adoptChildRenderers(PhpTemplateInterface $template): void {
        $sharedData = [];
        if (isset($this->rendererContainer)) {
            foreach ($this->childRenderers as $variableName => $rendererName) {
                $sharedData[$variableName] = $this->rendererContainer->get($rendererName);
            }
        }
        $template->setSharedData($sharedData);
    }


    /**
     *
     * @param type $rendererClassname
     * @param type $name Jméno proměnné v kompozitním view, která má být nahrazena výstupem zadané komponentní view
     */
    protected function addChildComponentWithRenderer($rendererClassname, $name) {
        // pokud render používá classMap musí být konfigurován v Renderer kontejneru - tam dostane classMap
        return $this->appendComponentView(
                (new AuthoredElement($this->configuration))->setData($this->contextData)->setRendererName($rendererClassname)->setRendererContainer($this->rendererContainer),
                $name
                );
    }
}
