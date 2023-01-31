<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\View\Content\Authored;

use Web\Component\View\ComponentCompositeAbstract;
use Configuration\ComponentConfigurationInterface;
/**
 * Description of AuthoredComponentAbstract
 * Objekt je potomkem CompositeView.
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends ComponentCompositeAbstract implements AuthoredComponentInterface {

    const BUTTON_EDIT_CONTENT = 'buttonEditContent';
    const SELECT_TEMPLATE = 'selectTemplate';

    public function __construct(ComponentConfigurationInterface $configuration) {
        parent::__construct($configuration);
    }
}
