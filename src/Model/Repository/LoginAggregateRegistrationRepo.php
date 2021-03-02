<?php
namespace Model\Repository;

use Model\Repository\LoginRepo;

use Model\Dao\LoginDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\RegistrationRepo;

use Model\Hydrator\LoginChildHydrator;

use Model\Entity\LoginAggregateRegistration;
use Model\Entity\LoginAggregateRegistrationInterface;
use Model\Entity\LoginInterface;
use Model\Entity\RegistrationInterface;

/**
 * Description of LoginAggregateRegistrationRepo
 *
 * @author vlse2610
 */
class LoginAggregateRegistrationRepo extends LoginRepo implements LoginRepoInterface {
   
    
    
    public function __construct(LoginDao $loginDao,      HydratorInterface $loginHydrator,
                                RegistrationRepo $registrationRepo,    LoginChildHydrator $loginRegistrationHydrator) {
        parent::__construct($loginDao, $loginHydrator);
        $this->registerOneToOneAssotiation(RegistrationInterface::class, 'login_name', $registrationRepo);
        $this->registerHydrator($loginRegistrationHydrator);
    }

    protected function createEntity() {
        return new LoginAggregateRegistration();
    }

    public function add(LoginInterface $loginAggregate) {
        /** @var LoginAggregateRegistrationInterface $loginAggregate */
        $this->addAssociated(RegistrationInterface::class, $loginAggregate); //add($loginAggregate->getRegistration()); <- do repo abstract
        parent::add($loginAggregate);
    }
    public function remove(LoginInterface $loginAggregate) {
        /** @var LoginAggregateRegistrationInterface $loginAggregate */
        $this->removeAssociated(RegistrationInterface::class, $loginAggregate); //add($loginAggregate->getRegistration()); <- do repo abstract
        parent::add($loginAggregate);
    }
    
}
