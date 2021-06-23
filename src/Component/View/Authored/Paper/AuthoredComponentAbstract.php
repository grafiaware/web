<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Paper;

use Component\View\CompositeComponentAbstract;
use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Pes\View\Template\PhpTemplate;

/**
 * Description of AuthoredComponentAbstract
 * Objekt je potomkem CompositeView.
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends CompositeComponentAbstract implements AuthoredComponentInterface {

    /**
     * @var bool
     */
    protected $editable;

    /**
     * @var string
     */
    private $templatesPath = "templates_path_not_set/";

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

    public function setTemplatesPath($templatesPath) {
        $this->templatesPath = $templatesPath;
    }

    public function addTemplateGlobals($variableName, $rendererName) {
        $this->templateGlobals[$variableName] = $rendererName;
    }

    protected function getPaperTemplatePath($templateName) {
        return $this->templatesPath.$templateName."/template.php";
    }

    public function setItemId($menuItemId): AuthoredComponentInterface {
        $this->contextData->setItemId($menuItemId);
        return $this;
    }
}
