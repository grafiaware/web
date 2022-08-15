<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\CompanyContact;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Dao\CompanyContactDao;
use Events\Model\Hydrator\CompanyContactHydrator;
use Events\Model\Repository\CompanyContactRepoInterface;



/**
 * Description of CompanyContactRepo
 *
 * @author vlse2610
 */
class CompanyContactRepo extends RepoAbstract implements CompanyContactRepoInterface {

    public function __construct(CompanyContactDao $companyContactDao, CompanyContactHydrator $companyContactHydrator) {
        $this->dataManager = $companyContactDao;
        $this->registerHydrator($companyContactHydrator);
    }
    
    
    /**
     *
     * @param type $id
     * @return CompanyContactInterface|null
     */
    public function get($id): ?CompanyContactInterface {   
        $key = $this->dataManager->getPrimaryKeyTouples(['id'=>$id]);
        return $this->getEntity($key);
    }
    
    

    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyContactInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     * 
     * @return CompanyContactInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

   /**
     * 
     * @param CompanyContactInterface $companyContact 
     * @return void
     */
    public function add( CompanyContactInterface $companyContact ) : void {
        $this->addEntity($companyContact);
    }
    
    
    /**
     * 
     * @param CompanyContactInterface $companyContact 
     * @return void
     */
    public function remove(CompanyContactInterface $companyContact)  :void {
        $this->removeEntity($companyContact);
    }
    
    
    

    protected function createEntity() : CompanyContactInterface {
        return new CompanyContact();
    }

    protected function indexFromEntity(CompanyContactInterface $companyContact) {
        return $companyContact->getId();
    }

    protected function indexFromRow( $row ) {
        return $row['id'];
    }
}

