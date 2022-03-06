<?php

namespace Red\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface ItemActionInterface extends EntityInterface {

    //TODO: CHYBÍ language code!
    public function getTypeFk(): string;

    public function getContentId(): string;

    public function getEditorLoginName(): string;

    public function getCreated(): \DateTime;

    public function setTypeFk($typeFk): ItemActionInterface;

    public function setContentId($itemId): ItemActionInterface;

    public function setEditorLoginName($editorLoginName): ItemActionInterface;

    public function setCreated(\DateTime $created): ItemActionInterface;
}
