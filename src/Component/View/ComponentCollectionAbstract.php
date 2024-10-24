<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View;

use Pes\View\CollectionView;

use Component\View\ComponentCollectionInterface;

use Configuration\ComponentConfigurationInterface;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;

/**
 * Description of CompositeComponentAbstract
 *
 * @author pes2704
 */
abstract class ComponentCollectionAbstract extends CollectionView implements ComponentCollectionInterface {

    /**
     *
     * @var ComponentConfigurationInterface
     */
    protected $configuration;

    public function __construct(ComponentConfigurationInterface $configuration) {
        $this->configuration = $configuration;
    }

    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => static::class, AccessPresentationEnum::EDIT => static::class],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => static::class],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => static::class]
        ];
    }
}
