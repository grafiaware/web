<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\CompositeView;

use Component\View\ComponentCompositeInterface;

use Configuration\ComponentConfigurationInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of CompositeComponentAbstract
 *
 * @author pes2704
 */
abstract class ComponentCompositeAbstract extends CompositeView implements ComponentCompositeInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;

    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }

    public static function getComponentPermissions(): array {
        return [];
    }
}
