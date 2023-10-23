<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\Company;
use Events\Model\Entity\CompanyInterface;
use Events\Model\Dao\CompanyDao;
use Events\Model\Hydrator\CompanyHydrator;
use Events\Model\Repository\CompanyRepoInterface;


/**
 * Description of CompanyRepo
 *
 * @author vlse2610
 */
class CompanyRepo extends RepoAbstract implements CompanyRepoInterface {

    public function __construct(CompanyDao $companyDao, CompanyHydrator $companyHydrator) {
        $this->dataManager = $companyDao;
        $this->registerHydrator($companyHydrator);

    }

    /**
     *
     * @param type $id
     * @return CompanyInterface|null
     */
    public function get($id): ?CompanyInterface {
        return $this->getEntity($id);
    }
    
    /**
     *
     * @param type $name
     * @return CompanyInterface|null
     */
    public function getByName($name): ?CompanyInterface {
        return $this->getEntityByUnique(['name'=>$name]);
    }
    
    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return CompanyInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

   /**
     *
     * @param CompanyInterface $company
     * @return void
     */
    public function add( CompanyInterface $company ) : void {
        $this->addEntity($company);
    }


    /**
     *
     * @param CompanyInterface $company
     * @return void
     */
    public function remove(CompanyInterface $company)  :void {
        $this->removeEntity($company);
    }




    protected function createEntity() : CompanyInterface {
        return new Company();
    }

    protected function indexFromEntity(CompanyInterface $company) {
        return $company->getId();
    }

    protected function indexFromRow( $row ) {
        return $row['id'];
    }
}
