<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\EntityInterface;
use Events\Model\Entity\EnrollInterface;
use Events\Model\Entity\Enroll;
use Events\Model\Dao\EnrollDao;
use Events\Model\Hydrator\EnrollHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class EnrollRepo extends RepoAbstract implements EnrollRepoInterface {

    public function __construct(EnrollDao $enrollDao, EnrollHydrator $enrollHydrator) {
        $this->dataManager = $enrollDao;
        $this->registerHydrator($enrollHydrator);
    }

    /**
     *
     * @param type $id
     * @return EnrollInterface|null
     */
    public function get($loginName, $eventId): ?EnrollDaoInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_login_name_fk' => $loginName, 'event_id_fk' => $eventId]);
        return $this->getEntity($key);
    }

    public function findByLoginName($loginName) {
        return $this->findEntities("login_name = :login_name", [":login_name"=>$loginName]);
    }

    public function findAll() {
        return $this->findEntities();
    }

    public function add(EnrollDaoInterface $enroll) {
        $this->addEntity($enroll);
    }

    public function remove(EnrollDaoInterface $enroll) {
        $this->removeEntity($enroll);
    }

    protected function createEntity() {
        return new Enroll();
    }

    protected function indexFromEntity(EnrollDaoInterface $enroll) {
        return $enroll->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
