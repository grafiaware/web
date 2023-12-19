<?php
namespace Red\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface AssetInterface extends PersistableEntityInterface {

    public function getId();

    public function getFilepath();

    public function getMimeType();

    public function getEditorLoginName();

    public function getCreated();

    public function getUpdated();

    public function setId($id): AssetInterface;

    public function setFilepath($filepath): AssetInterface;

    public function setMimeType($mimeType): AssetInterface;

    public function setEditorLoginName($editorLoginName): AssetInterface;

    public function setCreated($created): AssetInterface;

    public function setUpdated($created): AssetInterface;
}
