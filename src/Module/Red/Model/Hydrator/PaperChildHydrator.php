<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;
use Red\Model\Entity\PaperContentInterface;

/**
 * Description of PaperPaperContentAggregateHydrator
 *
 * @author pes2704
 */
class PaperChildHydrator implements HydratorInterface {

    /**
     * Nastaví do agregátu contents, pokud existuje. Contents jsou závislé na kontextu a tedy mohou být null (neaktivní nebo neaktuální content) a pole může být prázdné
     *
     * @param PaperAggregatePaperContentInterface $menuItemPaperAggregate
     * @param type $rowData
     */
    public function hydrate(EntityInterface $paperPaperContentsAggregate, RowDataInterface $rowData) {
        /** @var PaperAggregatePaperContentInterface $menuItemPaperAggregate */
        $paperPaperContentsAggregate
            ->exchangePaperContentsArray($rowData->offsetGet(PaperContentInterface::class));
    }

    /**
     *
     * @param PaperAggregatePaperContentInterface $paperAggregate
     * @param type $rowData
     */
    public function extract(EntityInterface $paperAggregate, RowDataInterface $rowData) {
        /** @var PaperAggregatePaperContentInterface $paperAggregate */
        $rowData->offsetSet(PaperContentInterface::class, $paperAggregate->getPaperContentsArray());
    }}
