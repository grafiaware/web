<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
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
     * @param type $row
     */
    public function hydrate(EntityInterface $paperPaperContentsAggregate, &$row) {
        /** @var PaperAggregatePaperContentInterface $menuItemPaperAggregate */
        $paperPaperContentsAggregate
            ->exchangePaperContentsArray($row[PaperContentInterface::class]);
    }

    /**
     *
     * @param PaperAggregatePaperContentInterface $paperAggregate
     * @param type $row
     */
    public function extract(EntityInterface $paperAggregate, &$row) {
        /** @var PaperAggregatePaperContentInterface $paperAggregate */
        $row[PaperContentInterface::class] = $paperAggregate->getPaperContentsArray();
    }}
