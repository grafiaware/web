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

    const BUTTON_EDIT_CONTENT = 'buttonEditContent';

    /**
     * @var AuthoredViewModelInterface
     */
    protected $contextData;

    public function setItemId($menuItemId): AuthoredComponentInterface {
        $this->contextData->setMenuItemId($menuItemId);
        return $this;
    }
}
