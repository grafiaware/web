<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Service\ContentGenerator;

use Service\ContentGenerator\Exception;

use Red\Model\Repository\MenuItemTypeRepo;

/**
 * Description of ContentGeneratorFactory
 *
 * @author pes2704
 */
class ContentGeneratorRegistry implements ContentGeneratorRegistryInterface {

    /**
     * @var MenuItemTypeRepo
     */
    private $menuItemTypeRepo;

    /**
     *
     * @var callable[]
     */
    private $serviceRegister;

    public function __construct(
            MenuItemTypeRepo $menuItemRepo
            ) {
        $this->menuItemTypeRepo = $menuItemRepo;
    }

    /**
     *
     * @param string $menuItemType
     * @param callable $service
     * @return void
     */
    public function registerService(string $menuItemType, callable $service): void {
        $this->serviceRegister[$menuItemType] = $service;
    }

    /**
     *
     * @param type $menuItemType
     * @return ContentServiceInterface
     * @throws Exception\UnknownMenuItemTypeException
     * @throws UnregisteredContentGeneretorException
     */
    public function getGenerator(string $menuItemType): ContentServiceInterface {
        $type = $this->menuItemTypeRepo->get($menuItemType);
        if (!isset($type)) {
            throw new Exception\UnknownMenuItemTypeException("Neznámý typ menu item $menuItemType. Nelze vytvořit generátor obsahu.");
        }
        if (array_key_exists($menuItemType, $this->serviceRegister)) {
            $serviceFactoryCallable = $this->serviceRegister[$menuItemType];
            return $serviceFactoryCallable();
        } else {
            throw new Exception\UnregisteredContentGeneretorException("Není zaregistrovaný generátor pro typ menu item '$menuItemType'");
        }

    }
}
