<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperPaperContentsAggregateInterface;

/**
 * Description of PaperPaperContentAggregateHydrator
 *
 * @author pes2704
 */
class PaperChildHydrator implements HydratorInterface {

    /**
     *
     * @param PaperPaperContentsAggregateInterface $menuItemPaperAggregate
     * @param type $row
     */
    public function hydrate(EntityInterface $paperPaperContentsAggregate, &$row) {
        /** @var PaperPaperContentsAggregateInterface $menuItemPaperAggregate */
        $paperPaperContentsAggregate
            ->exchangePaperContentsArray($row['contents']);
    }

    /**
     *
     * @param PaperPaperContentsAggregateInterface $paperAggregate
     * @param type $row
     */
    public function extract(EntityInterface $paperAggregate, &$row) {
        /** @var PaperPaperContentsAggregateInterface $paperAggregate */
        $row['contents'] = $paperAggregate->getPaperContentsArray();
    }}
