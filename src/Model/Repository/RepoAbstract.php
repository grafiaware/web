<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\DaoInterface;
use Model\Hydrator\HydratorInterface;

/**
 * Description of RepoAbstract
 *
 * @author pes2704
 */
abstract class RepoAbstract implements RepoInterface {

    protected $collection = [];
    protected $removed = [];

    protected $readOnly = false;

    /**
     * @var DaoInterface
     */
    protected $dao;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    public function setReadOnly($readOnly): RepoInterface {
        $this->readOnly = (bool) $readOnly;
        return $this;
    }

    public function isReadOnly() {
        return $this->readOnly;

    }

    public function flush() {
        /** @var \Model\Entity\EntityAbstract $entity */
        foreach ($this->collection as $menuItemId => $entity) {
            $row = [];
            $this->hydrator->extract($entity, $row);
            if ($entity->isPersisted()) {
                $this->dao->update($row);
            } else {
                $this->dao->insert($row);
                $entity->setPersisted();
            }
        }
        foreach ($this->removed as $entity) {
            $row = [];
            $this->hydrator->extract($entity, $row);
            $this->dao->delete($row);
            $entity->setUnpersisted();
        }
    }

    public function __destruct() {
        if (!$this->readOnly) {
            $this->flush();
        }
    }


}
