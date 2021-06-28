<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Pes\View\Template\PhpTemplate;
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

    /**
     * @var bool
     */
    protected $editable;

    /**
     * @var string
     */
    protected $templatesPath;

    protected $templateGlobals = [];

    /**
     * @var AuthoredViewModelInterface
     */
    protected $contextData;

    /**
     * @param AuthoredViewModelInterface $viewModel
     */
    public function __construct(AuthoredViewModelInterface $viewModel) {
        parent::__construct($viewModel);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplatesPath($templatesPath) {
        $this->templatesPath = $templatesPath;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubRendererName($variableName, $rendererName) {
        $this->templateGlobals[$variableName] = $rendererName;
    }

    public function setItemId($menuItemId): AuthoredComponentInterface {
        $this->contextData->setItemId($menuItemId);
        return $this;
    }

    public function getTemplatePath($templateName): string {
        if (isset($this->templatesPath))
        return $this->templatesPath.$templateName."/".self::DEFAULT_TEMPLATE_FILE_NAME;
    }

    /**
     *
     * @param string $templateName
     * @return PhpTemplateInterface|null
     * @throws NoTemplateFileException
     */
    protected function resolveTemplateAndShared($templateName=null): ?PhpTemplateInterface {
        if (isset($templateName) AND $templateName) {
            $templatePath = $this->getTemplatePath($templateName);
            $template = new PhpTemplate($templatePath);  // exception
//            $parentRendererClassMap = parent::resolveRenderer()->getClassMap();
            $sharedData = [];
            if (isset($this->rendererContainer)) {
                foreach ($this->templateGlobals as $variableName => $rendererName) {
                    $sharedData[$variableName] = $this->rendererContainer->get($rendererName);
                }
            }
            $template->setSharedData($sharedData);
        }

        return $template ?? null;
    }

}
