<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\PaperAggregateInterface;

/**
 * Description of PaperPaperContentAggregateHydrator
 *
 * @author pes2704
 */
class PaperChildHydrator implements HydratorInterface {

    /**
     * Nastaví do agregátu contents, pokud existuje. Contents jsou závislé na kontextu a tedy mohou být null (neaktivní nebo neaktuální content) a pole může být prázdné
     *
     * @param PaperAggregateInterface $menuItemPaperAggregate
     * @param type $row
     */
    public function hydrate(EntityInterface $paperPaperContentsAggregate, &$row) {
        /** @var PaperAggregateInterface $menuItemPaperAggregate */
        $paperPaperContentsAggregate
            ->exchangePaperContentsArray($row['contents']);
    }

    /**
     *
     * @param PaperAggregateInterface $paperAggregate
     * @param type $row
     */
    public function extract(EntityInterface $paperAggregate, &$row) {
        /** @var PaperAggregateInterface $paperAggregate */
        $row['contents'] = $paperAggregate->getPaperContentsArray();
    }}
