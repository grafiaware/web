<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;
use Red\Model\Entity\ActiveUserInterface;
use Red\Model\Entity\ActiveUser;
use Red\Model\Dao\ActiveUserDao;

use Pes\Type\Date;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class ActiveUserRepo {

    private $activeUserDao;

    public function __construct(ActiveUserDao $activeUserDao) {
        $this->activeUserDao = $activeUserDao;
    }


    /**
     *
     * @param type $idUser id active user
     * @return ActiveUserInterface|null
     */
    public function get($idUser): ?ActiveUserInterface {
        $row = $this->activeUserDao->get($idUser);
        $locker = function(ActiveUserInterface $activeUser) { return $this->activeUserDao->update($this->extract($activeUser)); };
        return $row ? $this->hydrate(new ActiveUser($locker), $row) : NULL;
    }

    public function add(ActiveUserInterface $activeUser) {
        $row = $this->extract($activeUser);
        if (!$this->get($activeUser->getUser())) {
            $this->activeUserDao->insert($row);
        }
        $this->activeUserDao->update($row);
    }

//    public function remove(ActiveUserInterface $activeUser) {
//        ;
//    }

    private function hydrate(ActiveUserInterface $user, $row) {
        $datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $row['akce']);
        return $user
                ->setUser($row['user'])
                ->setItemId($row['stranka'])
                ->setEditStartTime( ($datetime !== FALSE) ? $datetime : NULL );
    }

    private function extract(ActiveUserInterface $user) {
        return [
            'user' => $user->getUser(),
            'stranka' => $user->getItemId(),  // akce je timestamp - readonly
        ];
    }

    protected function indexFromEntity(MenuItemInterface $menuItem) {
        return $menuItem->getLangCodeFk().$menuItem->getUidFk();
    }

    protected function indexFromRow($row) {
        return $row['lang_code_fk'].$row['uid_fk'];
    }
}
