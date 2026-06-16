<?php

namespace Events\Service;

use Events\Service\LoginServiceInterface;
use Events\Model\Repository\LoginRepoInterface;
use Events\Model\Entity\Login;
use Events\Model\Entity\LoginInterface;
use Pes\Logger\FileLogger;


/**
 * Description of LoginService
 *
 * @author vlse2610
 */
class LoginService implements LoginServiceInterface {
    private $loginRepo;
    private $fileLogger;    
    
    public function __construct(                        
            LoginRepoInterface $loginRepo,  
            ?FileLogger $fileLogger = null           
            ) {        
        $this->loginRepo = $loginRepo;
        $this->fileLogger = $fileLogger;     
    } 
    
     
    #[\Override]
    public function setDeleteUserNameFromEventsLogin(string $loginName): void {            
        $login = $this->loginRepo->get($loginName);
        if ($login) {                                                
            $login->setDeletedDueToAuth('1');
            $login->setLoginName($loginName . '_deleted_' . date("Ymd_His") );    
        }                  
//        $this->fileLogger?->notice("* " .$loginName . " není validní uživatel (není v single_login)." .
//                                   " - a byl 'vymazán' z tabulky events.login (byl-li tam)" );                   
    }
        
    
    #[\Override]
    public function setAddUserNameToEventsLogin(string $loginName): void {      
        $login = $this->loginRepo->get($loginName);
        if ($login) {     //je v tabulce events.login                       
                    //   $this->fileLogger?->notice("Přihlašený " .$loginName . " je validní uživatel(v single_login)." .  " a je v tabulce events.login." );                                                                 
        }
        else {    //neni v tabulce events.login                                                
            /**  @var LoginInterface $login */
            $login =  new Login();
            $login->setDeletedDueToAuth(0);
            $login->setLoginName($loginName); 
            $this->loginRepo->add($login);                 
                    //    $this->fileLogger?->notice("Přihlašený " .$loginName . " je validní uživatel(v single_login)," . ' nebyl v tabulce events.login. -> a byl tam přidán.' );                             
        }           
//        $this->fileLogger?->notice("* " .$loginName . " je validní uživatel( je v single_login)," .
//                                       ' pokud nebyl v tabulce events.login. -> tak tam byl přidán.' );        
    }        
    
}
