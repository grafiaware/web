<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\UserInterface;
use Model\Entity\User;
use Model\Dao\UserOpravneniDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class UserRepo extends RepoAbstract implements RepoInterface {

    public function __construct(UserOpravneniDao $opravneniDao, HydratorInterface $userHydrator) {
        $this->dao = $opravneniDao;
        $this->registerHydrator($userHydrator);
    }

    /**
     *
     * @param type $userName
     * @return PaperInterface|null
     */
    public function get($userName): ?UserInterface {
        $index = $userName;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($userName));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function add(UserInterface $user) {
        $this->collection[$this->indexFromEntity($user)] = $user;
    }

    public function remove(UserInterface $user) {
        $this->removed[] = $user;
        unset($this->collection[$this->indexFromEntity($user)]);
    }

    protected function createEntity() {
        return new User();
    }

    protected function indexFromEntity(UserInterface $user) {
        return $user->getUserName();
    }

    protected function indexFromRow($row) {
        return $row['user'];
    }

}

