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
interface PaperAggregateInterface extends PaperInterface {

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

    public function exchangePaperContentsArray(array $contents=[]): PaperAggregateInterface;
}
