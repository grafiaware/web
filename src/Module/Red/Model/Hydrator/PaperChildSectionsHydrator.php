<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use ArrayAccess;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Entity\PaperSectionInterface;

/**
 * Description of PaperPaperContentAggregateHydrator
 *
 * @author pes2704
 */
class PaperChildSectionsHydrator implements HydratorInterface {

    /**
     * Nastaví do agregátu contents, pokud existuje. Contents jsou závislé na kontextu a tedy mohou být null (neaktivní nebo neaktuální content) a pole může být prázdné
     *
     * @param PaperAggregatePaperSectionInterface $menuItemPaperAggregate
     * @param type $rowData
     */
    public function hydrate(EntityInterface $paperPaperContentsAggregate, ArrayAccess $rowData) {
        /** @var PaperAggregatePaperSectionInterface $menuItemPaperAggregate */
        $paperPaperContentsAggregate
            ->exchangePaperContentsArray($rowData[0]);
    }

    /**
     *
     * @param PaperAggregatePaperSectionInterface $paperAggregate
     * @param type $rowData
     */
    public function extract(EntityInterface $paperAggregate, ArrayAccess $rowData) {
        /** @var PaperAggregatePaperSectionInterface $paperAggregate */
        $rowData[0] = $paperAggregate->getPaperContentsArray();
    }}
