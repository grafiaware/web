<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GeneratorService\Paper;

use Model\Entity\PaperInterface;

/**
 *
 * @author pes2704
 */
interface PaperServiceInterface {
    public function create($menuItemIdFk): PaperInterface;
    public function remove(PaperInterface $paperAggregate);
}
