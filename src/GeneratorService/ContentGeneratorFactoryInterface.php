 <?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GeneratorService;

/**
 *
 * @author pes2704
 */
interface ContentGeneratorFactoryInterface {
    
    /**
     *
     * @param type $name
     * @param callable $service
     */
    public function registerGeneratorService($name, callable $service);

    /**
     *
     * @param string $menuItemType
     */
    public function create($menuItemType);

    }
