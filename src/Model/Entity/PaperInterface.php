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
 * Description of PaperInterface
 *
 * @author pes2704
 */
interface PaperInterface extends EntityInterface {

    /**
     *
     */
    public function getMenuItemIdFk();

    /**
     *
     * @return PaperHeadline
     */
    public function getHeadline(): PaperHeadline;

    /**
     *
     * @return PaperContent array of
     */
    public function getContents(): array;

    /**
     *
     * @param type $menuItemIdFk
     * @return \Model\Entity\PaperInterface
     */
    public function setMenuItemIdFk($menuItemIdFk): PaperInterface;

    /**
     *
     * @param PaperHeadline $headline
     * @return PaperInterface
     */
    public function setHeadline(PaperHeadline $headline): PaperInterface;

    /**
     *
     * @param array $contents
     * @return PaperInterface
     */
    public function setContents(array $contents=[]): PaperInterface;
}
