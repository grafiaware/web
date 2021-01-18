<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ContentService\Paper;

use Model\Entity\PaperAggregateInterface;

/**
 *
 * @author pes2704
 */
interface PaperAggregateServiceInterface {
    public function create(): PaperAggregateInterface;
    public function remove(PaperAggregateInterface $paperAggregate);
}
