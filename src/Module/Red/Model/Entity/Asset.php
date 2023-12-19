<?php
namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Red\Model\Entity\MenuItemAssetInterface;

/**
 * Description of Asset
 *
 * @author pes2704
 */
class Asset extends PersistableEntityAbstract implements AssetInterface {

    private $id;
    private $filepath;
    private $mimeType;
    private $editorLoginName;
    private $created;
    private $updated;

    public function getId() {
        return $this->id;
    }

    public function getFilepath() {
        return $this->filepath;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function getEditorLoginName() {
        return $this->editorLoginName;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setId($id): MenuItemAssetInterface {
        $this->id = $$id;
        return $this;
    }

    public function setFilepath($filepath): MenuItemAssetInterface {
        $this->filepath = $filepath;
        return $this;
    }

    public function setMimeType($mimeType): MenuItemAssetInterface  {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function setEditorLoginName($editorLoginName): MenuItemAssetInterface {
        $this->editorLoginName = $editorLoginName;
        return $this;
    }

    public function setCreated($created): MenuItemAssetInterface {
        $this->created = $created;
        return $this;
    }

    public function setUpdated($updated): MenuItemAssetInterface {
        $this->updated = $updated;
        return $this;
    }
}
