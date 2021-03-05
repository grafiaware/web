<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface PaperAggregatePaperContentInterface extends PaperInterface {

    const BY_PRIORITY = 'sort descending by content priority (highest on top)';

    /**
     *
     * @param type $id
     * @return PaperContentInterface|null
     */
    public function getPaperContent($id): ?PaperContentInterface;

    /**
     *
     * @return PaperContentInterface array of
     */
    public function getPaperContentsArray(): array;

    /**
     *
     * @return PaperContentInterface array of
     */
    public function getPaperContentsArraySorted($sortType): array;

    public function exchangePaperContentsArray(array $contents=[]): PaperAggregatePaperContentInterface;
}
