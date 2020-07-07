<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of PaperPaperContentsAggregate
 *
 * @author pes2704
 */
class PaperAggregate extends Paper implements PaperAggregateInterface {

    /**
     * @var PaperContent array of
     */
    private $contents = [];

    /**
     *
     * @param int $id id paper content
     * @return PaperContentInterface|null
     */
    public function getPaperContent($id): ?PaperContentInterface {
        return array_key_exists($id, $this->contents) ? $this->contents[$id] : null;
    }

    /**
     *
     * @return PaperContentInterface array of
     */
    public function getPaperContentsArray(): array {
        return $this->contents;
    }

    /**
     *
     * @param array $contents
     * @return \Model\Entity\MenuItemPaperAggregateInterface
     */
    public function exchangePaperContentsArray(array $contents=[]): PaperAggregateInterface {
        $this->contents = $contents;
        return $this;
    }
}
