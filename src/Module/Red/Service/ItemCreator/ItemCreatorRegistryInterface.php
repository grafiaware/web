<?php
namespace Red\Service\ItemCreator;

/**
 *
 * @author pes2704
 */
interface ItemCreatorRegistryInterface {

    const EMPTY_MENU_ITEM_TYPE = 'empty';

    /**
     * 
     * @param string $menuItemApiModule
     * @param string $menuItemApiGenerator
     * @param callable $service
     * @return void
     */
    public function registerGenerator(string $menuItemApiModule, string $menuItemApiGenerator, callable $service): void;

    /**
     * 
     * @param string $menuItemApiModule
     * @param string $menuItemApiGenerator
     * @return ItemCreatorInterface
     */
    public function getGenerator(string $menuItemApiModule, string $menuItemApiGenerator): ItemCreatorInterface ;

    }
