<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GeneratorService;

use GeneratorService\Exception;

use Model\Repository\MenuItemTypeRepo;

/**
 * Description of ContentGeneratorFactory
 *
 * @author pes2704
 */
class ContentGeneratorFactory extends ContentServiceAbstract implements ContentGeneratorFactoryInterface {

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
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemTypeRepo $menuItemRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->menuItemTypeRepo = $menuItemRepo;
    }

    /**
     *
     * @param type $name
     * @param callable $service
     */
    public function registerGeneratorService($name, callable $service) {
        $this->serviceRegister[$name] = $service;
    }

    /**
     *
     * @param string $menuItemType
     */
    public function create($menuItemType) {
        $type = $this->menuItemTypeRepo->get($menuItemType);
        if (!isset($type)) {
            throw new Exception\UnknownMenuItemTypeException("Neznámý typ menu item $menuItemType. Nelze vytvořit generátor obsahu.");
        }


    }
}
