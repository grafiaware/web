<?php
namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Red\Model\Entity\AssetInterface;

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

    public function setId($id): AssetInterface {
        $this->id = $id;
        return $this;
    }

    public function setFilepath($filepath): AssetInterface {
        $this->filepath = $filepath;
        return $this;
    }

    public function setMimeType($mimeType): AssetInterface  {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function setEditorLoginName($editorLoginName): AssetInterface {
        $this->editorLoginName = $editorLoginName;
        return $this;
    }

    public function setCreated($created): AssetInterface {
        $this->created = $created;
        return $this;
    }

    public function setUpdated($updated): AssetInterface {
        $this->updated = $updated;
        return $this;
    }
}
