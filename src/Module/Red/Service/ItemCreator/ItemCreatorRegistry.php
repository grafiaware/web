<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Service\ItemCreator;

use Red\Service\ItemCreator\Exception;

use Red\Model\Repository\MenuItemApiRepo;
use Red\Service\ItemCreator\Enum\ItemApiGeneratorEnum;
use Pes\Type\Exception\ValueNotInEnumException;

/**
 * Description of ContentGeneratorFactory
 *
 * @author pes2704
 */
class ItemCreatorRegistry implements ItemCreatorRegistryInterface {

    /**
     * @var MenuItemApiRepo
     */
    private $menuItemApiRepo;

    /**
     *
     * @var callable[]
     */
    private $serviceRegister;

    public function __construct(
            MenuItemApiRepo $menuItemApiRepo
            ) {
        $this->menuItemApiRepo = $menuItemApiRepo;
    }

    /**
     *
     * @param string $menuItemApiModule
     * @param callable $service
     * @return void
     */
    public function registerGenerator(string $menuItemApiModule, string $menuItemApiGenerator, callable $service): void {
        $this->serviceRegister[$menuItemApiModule][$menuItemApiGenerator] = $service;
    }

    /**
     * 
     * @param string $menuItemApiModule
     * @return ItemCreatorInterface
     * @throws Exception\UnknownMenuItemTypeException
     * @throws Exception\UnregisteredContentGeneretorException
     */
    public function getGenerator(string $menuItemApiModule, string $menuItemApiGenerator): ItemCreatorInterface {
//        $enumType = (new ItemGeneratorEnum())($menuItemApiModule);
        $type = $this->menuItemApiRepo->get($menuItemApiModule, $menuItemApiGenerator);
        if (!isset($type)) {
            throw new Exception\UnknownMenuItemTypeException("Neznámý api module nebo api generator - '$menuItemApiModule','$menuItemApiGenerator'  nenalezen v databázi. Nelze vytvořit generátor obsahu.");
        }
        if (isset($this->serviceRegister[$menuItemApiModule][$menuItemApiGenerator])) {
            $serviceFactoryCallable = $this->serviceRegister[$menuItemApiModule][$menuItemApiGenerator];
            return $serviceFactoryCallable();
        } else {
            throw new Exception\UnregisteredContentGeneretorException("Není zaregistrovaný generátor pro api module a api generator '$menuItemApiModule','$menuItemApiGenerator'");
        }

    }
}
