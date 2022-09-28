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
     * @param string $name
     * @param callable $service
     * @return void
     */
    public function registerGenerator(string $name, callable $service): void;

    /**
     *
     * @param string $menuItemType
     * @return ItemCreatorInterface
     */
    public function getGenerator(string $menuItemType): ItemCreatorInterface ;

    }
