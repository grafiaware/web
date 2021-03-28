<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\EntityInterface;
use Model\Entity\EnrollInterface;
use Model\Entity\Enroll;
use Model\Dao\EnrollDao;
use Model\Hydrator\EnrollHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class EnrollRepo extends RepoAbstract implements EnrollRepoInterface {

    public function __construct(EnrollDao $enrollDao, EnrollHydrator $enrollHydrator) {
        $this->dao = $enrollDao;
        $this->registerHydrator($enrollHydrator);
    }

    /**
     *
     * @param type $id
     * @return EnrollInterface|null
     */
    public function get($id): ?EnrollInterface {
        $index = $id;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($id));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function findByLoginName($loginName) {
        $selected = [];
        foreach ($this->dao->findAll("login_name = $loginName") as $enrollRow) {
            $index = $this->indexFromRow($enrollRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $enrollRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function findAll() {
        $selected = [];
        foreach ($this->dao->findAll() as $enrollRow) {
            $index = $this->indexFromRow($enrollRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $enrollRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function add(EnrollInterface $enroll) {
        $this->addEntity($enroll);
    }

    public function remove(EnrollInterface $enroll) {
        $this->removeEntity($enroll);
    }

    protected function createEntity() {
        return new Enroll();
    }

    protected function indexFromEntity(EnrollInterface $enroll) {
        return $enroll->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
