<?php
namespace Events\Model\Repository;

use Model\Repository\RepoAbstract;

use Events\Model\Entity\Representative;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Dao\RepresentativeDao;
use Events\Model\Hydrator\RepresentativeHydrator;
use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Entity\Company;


/**
 * Description of RepresentativeRepo
 *
 * @author vlse2610
 */
class RepresentativeRepo  extends RepoAbstract implements RepresentativeRepoInterface {

    public function __construct(RepresentativeDao $representativeDao, RepresentativeHydrator $representativeHydrator) {
        $this->dataManager = $representativeDao;
        $this->registerHydrator($representativeHydrator);
    }


    /**
     *
     * @param type $loginLoginName
     * @param type $companyId
     * @return RepresentativeInterface|null
     */
    public function get($loginLoginName, $companyId): ?RepresentativeInterface {
        return $this->getEntity($loginLoginName, $companyId);
    }

    /**
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkazu where
     * 
     * @return RepresentativeInterface[]
     */
    public function find($whereClause="", $touplesToBind=[]): array {
        return $this->findEntities($whereClause, $touplesToBind);
    }

    /**
     *
     * @return RepresentativeInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }


    /**
     *
     * @param string $companyId
     * @return RepresentativeInterface[]
     */
    public function findByCompany($companyId) : array {
        return $this->findEntities("company_id = :company_id", [":company_id"=>$companyId] );
    }

    /**
     * 
     * @param string $loginName
     * @return RepresentativeInterface[]
     */
    public function findByLoginName($loginName) : array {
        return $this->findEntities("login_login_name = :login_login_name", [":login_login_name"=>$loginName] );
    }    

   /**
     *
     * @param RepresentativeInterface $representative
     * @return void
     */
    public function add( RepresentativeInterface $representative ) : void {
        $this->addEntity($representative);
    }


    /**
     *
     * @param RepresentativeInterface $representative
     * @return void
     */
    public function remove(RepresentativeInterface $representative)  :void {
        $this->removeEntity($representative);
    }




    protected function createEntity() : RepresentativeInterface {
        return new Representative();
    }

    protected function indexFromEntity(RepresentativeInterface $representative) {
        return $representative->getLoginLoginName() . $representative->getCompanyId() ;
    }

    protected function indexFromRow( $row ) {
        return $row['login_login_name'] . $row['company_id'];
    }
}



