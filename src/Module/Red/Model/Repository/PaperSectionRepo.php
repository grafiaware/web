<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Entity\PaperSectionInterface;
use Red\Model\Entity\PaperSection;
use Red\Model\Dao\PaperSectionDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\RepoAssotiatedManyTrait;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class PaperSectionRepo extends RepoAbstract implements PaperSectionRepoInterface {

    protected $dao;

    public function __construct(PaperSectionDao $paperContentDao, HydratorInterface $contentHydrator) {
        $this->dataManager = $paperContentDao;
        $this->registerHydrator($contentHydrator);
    }

    use RepoAssotiatedManyTrait;

    /**
     *
     * @param int $contentId
     * @return PaperSectionInterface|null
     */
    public function get($id): ?PaperSectionInterface {
        return $this->getEntity($id);
    }

    /**
     *
     * @param type $paperIdFk
     * @return iterable
     */
    public function findByPaperIdFk($paperIdFk): iterable {
        return $this->findEntityByReference(PaperSectionDao::REFERENCE_PAPER, $paperIdFk);
//        $key = $this->dataManager->getForeignKeyTouples('paper_id_fk', ['paper_id_fk'=>$paperIdFk]);
//        return $this->findEntitiesByReference('paper_id_fk', $key);

//            public function findByJobTagTag($jobTagTag) : array  {
//        return $this->findEntities("job_tag_tag = :job_tag_tag", [":job_tag_tag"=>$jobTagTag]);
//    }
    }

    public function add(PaperSectionInterface $paperContent) {
        $this->addEntity($paperContent);

    }

    public function remove(PaperSectionInterface $paperContent) {
        $this->removeEntity($paperContent);
    }

    protected function createEntity() {
        return new PaperSection();
    }

    protected function indexFromEntity(PaperSectionInterface $paperContent) {
        return $paperContent->getId();
    }

    protected function indexFromRow($row) {
        return $row['id'];
    }


}
