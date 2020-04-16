<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\UserInterface;
use Model\Entity\User;
use Model\Entity\UserActions;

use Model\Dao\UserOpravneniDao;

use Pes\Type\Date;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class UserRepo {

    private $userOpravneniDao;

    public function __construct(UserOpravneniDao $userDao) {
        $this->userOpravneniDao = $userDao;
    }

    //TODO: Svoboda Je nutné vytvořit novou tabulku user místo opravneni a v ní zajistit, že user bude unique!
    /**
     * Vrací entitu User vyhledanou podle jména. Předpokládá unikátní hodnoty v tabulce ve sloupci user.
     *
     * @param type $user
     * @return UserInterface|null
     */
    public function get($user): ?UserInterface {
        $row = $this->userOpravneniDao->get($user);
        return $row ? $this->createItem($row) : NULL;
    }

    /**
     * Vrací entitu User vyhledanou podle jména a hesla.
     *
     * @param type $user
     * @param type $password
     * @return UserInterface
     */
    public function getByAuthentication($user, $password) {
        $row = $this->userOpravneniDao->getByAuthentication($user, $password);
        return $row ? $this->createItem($row) : NULL;
    }

    private function createItem($row) {
        return (new User())
                ->setUserName($row['user'])
                ->setRole($row['role']);
    }

    public function add(UserInterface $user) {
        ;
    }

    public function remove(UserInterface $user) {
        ;
    }
}
