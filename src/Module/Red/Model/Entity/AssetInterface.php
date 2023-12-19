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

    public function setId($id): MenuItemAssetInterface;

    public function setFilepath($filepath): MenuItemAssetInterface;

    public function setMimeType($mimeType): MenuItemAssetInterface;

    public function setEditorLoginName($editorLoginName): MenuItemAssetInterface;

    public function setCreated($created): MenuItemAssetInterface;

    public function setUpdated($created): MenuItemAssetInterface;
}
