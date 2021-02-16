<?php

namespace GeneratorService;

/**
 *
 * @author pes2704
 */
interface ContentGeneratorRegistryInterface {

    const EMPTY_MENU_ITEM_TYPE = 'empty';

    /**
     *
     * @param string $name
     * @param callable $service
     * @return void
     */
    public function registerGeneratorService(string $name, callable $service): void;

    /**
     *
     * @param string $menuItemType
     * @return ContentServiceInterface
     */
    public function getGenerator(string $menuItemType): ContentServiceInterface ;

    }
