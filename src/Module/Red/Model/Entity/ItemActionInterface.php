<?php

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface ItemActionInterface extends PersistableEntityInterface {

    public function getItemId(): string;

    public function getEditorLoginName(): string;

    public function getCreated(): \DateTime;

    public function setItemId($itemId): ItemActionInterface;

    public function setEditorLoginName($editorLoginName): ItemActionInterface;

    public function setCreated(\DateTime $created): ItemActionInterface;
}
