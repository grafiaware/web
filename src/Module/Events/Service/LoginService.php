<?php

namespace Events\Service;

use Events\Service\LoginServiceInterface;
use Events\Model\Repository\LoginRepoInterface;
use Events\Model\Entity\Login;
use Events\Model\Entity\LoginInterface;

/**
 * Description of LoginService
 *
 * @author vlse2610
 */
class LoginService implements LoginServiceInterface {
    //private $loginRepo;
    
    public function __construct(                        
           // LoginRepoInterface $loginRepo,    
            ) {        
        //$this->loginRepo = $loginRepo;
    } 
    
     
    #[\Override]
    public function setDeleteUserNameFromEventsLogin(LoginInterface $login): void {        
        $login->setDeletedDueToAuth('1');
        $login->setLoginName($login->getLoginName() . '_deleted_' . date("Ymd_His") );                 
    }
        
    
    #[\Override]
    public function setAddUserNameToEventsLogin(LoginInterface $login /*string $loginName*/): void {        
        /**  @var LoginInterface $loginA */
        //$loginA =  new Login();
        $login->setDeletedDueToAuth(0);
        //$login->setLoginName($loginName); 

        //$this->loginRepo->add($login);        
    }
    
    
    
}
