<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\CompanyParameter;
use Events\Model\Entity\CompanyParameterInterface;
use Events\Model\Dao\CompanyParameterDao;
use Events\Model\Hydrator\CompanyParameterHydrator;
use Events\Model\Repository\CompanyParameterRepoInterface;


/**
 * Description of CompanyParameterRepo
 *
 * @author vlse2610
 */
class CompanyParameterRepo  extends RepoAbstract implements CompanyParameterRepoInterface {

    public function __construct(CompanyParameterDao $companyParameterDao, CompanyParameterHydrator $companyParameterHydrator) {
        $this->dataManager = $companyParameterDao;
        $this->registerHydrator($companyParameterHydrator);
    }


    /**
     *
     * @param type $company_id
     * @return CompanyParameterInterface|null
     */
    public function get($company_id): ?CompanyParameterInterface {
        return $this->getEntity($company_id);
    }

    /**
     *
     * @param type $whereClause
     * @param type $touplesToBind
     * @return CompanyParameterInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return CompanyParameterInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

   /**
     *
     * @param CompanyParameterInterface $companyParameter
     * @return void
     */
    public function add( CompanyParameterInterface $companyParameter ) : void {
        $this->addEntity($companyParameter);
    }


    /**
     *
     * @param CompanyParameterInterface $companyParameter
     * @return void
     */
    public function remove(CompanyParameterInterface $companyParameter)  :void {
        $this->removeEntity($companyParameter);
    }

    protected function createEntity() : CompanyParameterInterface {
        return new CompanyParameter();
    }

    protected function indexFromEntity(CompanyParameterInterface $companyParameter) {
        return $companyParameter->getCompanyId();
    }

    protected function indexFromRow( $row ) {
        return $row['company_id'];
    }
}


