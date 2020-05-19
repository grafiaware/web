<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\PaperInterface;
use Model\Entity\PaperHeadline;
use Model\Entity\PaperContent;

/**
 * Description of Article
 *
 * @author pes2704
 */
class Paper extends EntityAbstract implements PaperInterface {

    private $menuItemIdFk;

    /**
     * @var PaperHeadline
     */
    private $headline;

    /**
     * @var PaperContent array of
     */
    private $contents = [];

    /**
     *
     * @return type
     */
    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    /**
     *
     * @return PaperHeadline
     */
    public function getPaperHeadline(): PaperHeadline {
        return $this->headline;
    }

    /**
     *
     * @param int $id id paper content
     * @return PaperContent|null
     */
    public function getPaperContent($id): ?PaperContent {
        return array_key_exists($id, $this->contents) ? $this->contents[$id] : null;
    }

    /**
     *
     * @return PaperContent array of
     */
    public function getPaperContentsArray(): array {
        return $this->contents;
}

    /**
     *
     * @param type $menuItemIdFk
     * @return PaperHeadline
     */
    public function setMenuItemIdFk($menuItemIdFk): PaperInterface {
        $this->menuItemIdFk = $menuItemIdFk;
        return $this;
    }

    /**
     *
     * @param PaperHeadline $headline
     * @return \Model\Entity\PaperInterface
     */
    public function setPaperHeadline(PaperHeadline $headline): PaperInterface {
        $this->headline = $headline;
        return $this;
    }

    /**
     *
     * @param array $contents
     * @return \Model\Entity\PaperInterface
     */
    public function exchangePaperContentsArray(array $contents=[]): PaperInterface {
        $this->contents = $contents;
        return $this;
    }
}
