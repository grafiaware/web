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
class PaperAggregatePaperContent extends Paper implements PaperAggregateInterface {

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
     * @return PaperContentInterface array of
     */
    public function getPaperContentsArraySorted($sortType = self::BY_PRIORITY): array {
        $contents = $this->contents;
        switch ($sortType) {
            case self::BY_PRIORITY :
                \usort($contents, array($this, "compareByPriority"));
                break;

            default:
                break;
        }
        return $contents;
    }
    /**
     *
     * @param array $contents
     * @return \Model\Entity\MenuItemAggregatePaperInterface
     */
    public function exchangePaperContentsArray(array $contents=[]): PaperAggregateInterface {
        $this->contents = $contents;
        return $this;
    }


    /**
     * Compare funkce pro usort - řadí shora od nejvyšší priority
     *
     * @param PaperContentInterface $c1
     * @param PaperContentInterface $c2
     * @return int
     */
    private function compareByPriority($c1, $c2) {
        /** @var PaperContentInterface $c1 */
        /** @var PaperContentInterface $c2 */
        if ($c1->getPriority() == $c2->getPriority()) {
            return 0;
        }
        // desc !
        return ($c1->getPriority() > $c2->getPriority()) ? -1 : 1;
    }
}
