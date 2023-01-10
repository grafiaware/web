<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

/**
 * Description of PaperPaperContentsAggregate
 *
 * @author pes2704
 */
class PaperAggregatePaperSection extends Paper implements PaperAggregatePaperSectionInterface {

    /**
     * @var PaperSection array of
     */
    private $contents = [];

    /**
     *
     * @param int $id id paper content
     * @return PaperSectionInterface|null
     */
    public function getPaperSection($id): ?PaperSectionInterface {
        return array_key_exists($id, $this->contents) ? $this->contents[$id] : null;
    }

    /**
     *
     * @return PaperSectionInterface array of
     */
    public function getPaperContentsArray(): array {
        return $this->contents;
    }

    /**
     *
     * @return PaperSectionInterface array of
     */
    public function getPaperSectionsArraySorted($sortType = self::BY_PRIORITY): array {
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
    public function exchangePaperSectionsArray(array $contents=[]): PaperAggregatePaperSectionInterface {
        $this->contents = $contents;
        return $this;
    }


    /**
     * Compare funkce pro usort - řadí shora od nejvyšší priority
     *
     * @param PaperSectionInterface $c1
     * @param PaperSectionInterface $c2
     * @return int
     */
    private function compareByPriority($c1, $c2) {
        /** @var PaperSectionInterface $c1 */
        /** @var PaperSectionInterface $c2 */
        if ($c1->getPriority() == $c2->getPriority()) {
            return 0;
        }
        // desc !
        return ($c1->getPriority() > $c2->getPriority()) ? -1 : 1;
    }
}
