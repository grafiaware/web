<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

/**
 *
 * @author pes2704
 */
interface PaperAggregatePaperSectionInterface extends PaperInterface {

    const BY_PRIORITY = 'sort descending by content priority (highest on top)';

    /**
     *
     * @param type $id
     * @return PaperSectionInterface|null
     */
    public function getPaperSection($id): ?PaperSectionInterface;

    /**
     *
     * @return PaperSectionInterface[] array of
     */
    public function getPaperSectionsArray(): array;

    /**
     *
     * @return PaperSectionInterface[] array of
     */
    public function getPaperSectionsArraySorted($sortType): array;

    public function setPaperSectionsArray(array $contents=[]): PaperAggregatePaperSectionInterface;
}
