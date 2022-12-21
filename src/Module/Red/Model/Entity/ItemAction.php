<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

/**
 * Description of ItemAction
 *
 * @author pes2704
 */
class ItemAction extends PersistableEntityAbstract implements ItemActionInterface {

    private $typeFk;
    private $itemId;
    private $editorLoginName;
    private $created;

    public function getTypeFk(): string {
        return $this->typeFk;
    }

    public function getItemId(): string {
        return $this->itemId;
    }

    public function getEditorLoginName(): string {
        return $this->editorLoginName;
    }

    public function getCreated(): \DateTime {
        return $this->created;
    }

    public function setTypeFk($typeFk): ItemActionInterface {
        $this->typeFk = $typeFk;
        return $this;
    }

    public function setItemId($itemId): ItemActionInterface {
        $this->itemId = $itemId;
        return $this;
    }

    public function setEditorLoginName($editorLoginName): ItemActionInterface {
        $this->editorLoginName = $editorLoginName;
        return $this;
    }

    public function setCreated(\DateTime $created): ItemActionInterface {
        $this->created = $created;
        return $this;
    }


}
