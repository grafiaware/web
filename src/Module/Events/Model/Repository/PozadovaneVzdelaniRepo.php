<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;
//use Model\Hydrator\PozadovaneVzdelaniHydrator;

use Events\Model\Hydrator\PozadovaneVzdelaniHydrator;
use Events\Model\Entity\PozadovaneVzdelani;
use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;



/**
 * Description of PozadovaneVzdelaniRepo
 *
 * @author vlse2610
 */
class PozadovaneVzdelaniRepo extends RepoAbstract implements PozadovaneVzdelaniRepoInterface {

   

    public function __construct( PozadovaneVzdelaniDao $pozadovaneVzdelaniDao, PozadovaneVzdelaniHydrator $pozadovaneVzdelaniHydrator) {
        $this->dataManager = $pozadovaneVzdelaniDao;
        $this->registerHydrator($pozadovaneVzdelaniHydrator);
    }
   
     /**
     * 
     * @param  $stupen
     * @return PozadovaneVzdelaniInterface|null
     */
    public function get( $stupen): ?PozadovaneVzdelaniInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['stupen'=>$stupen ]);
        return $this->getEntity($key);    }

    /**
     * 
     * @return PozadovaneVzdelaniInterface[]
     */  
    public function findAll() :array  {
        return $this->findEntities();
    }
    /**
     * 
     * @param PozadovaneVzdelaniInterface $stupen
     * @return void
     */
    public function add(PozadovaneVzdelaniInterface $pozadovaneVzdelani) :void {
        $this->addEntity($pozadovaneVzdelani);
    }
    
     /**
     * 
     * @param PozadovaneVzdelaniInterface $stupen
     * @return void
     */
    public function remove(PozadovaneVzdelaniInterface $pozadovaneVzdelani) :void {
        $this->removeEntity($pozadovaneVzdelani);
    }
    
    

    protected function createEntity() {
        return new PozadovaneVzdelani();
    }

    protected function indexFromEntity(PozadovaneVzdelaniInterface $pozadovaneVzdelani) {
        return $pozadovaneVzdelani->getStupen();
    }

    protected function indexFromRow($row) {
        return $row['stupen'];
    }
}
