<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;


use Auth\Model\Repository\CredentialsRepoInterface;
use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\RoleRepoInterface;
use Auth\Model\Repository\RoleRepo;
use Auth\Model\Repository\LoginRepo;
use Auth\Model\Repository\LoginRepoInterface;

use Auth\Model\Entity\RoleInterface;
use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\LoginInterface;


use Auth\Middleware\Login\Controller\AuthController;



/** @var PhpTemplateRendererInterface $this */

    /** @var CredentialsRepoInterface $credentialsRepo */ 
    $credentialsRepo = $container->get(CredentialsRepo::class );
    /** @var RoleRepoInterface $roleRepo */ 
    $roleRepo = $container->get(RoleRepo::class );
    /** @var LoginRepoInterface $loginRepo */ 
    $loginRepo = $container->get(LoginRepo::class );
      
     
    $selectLoginNames = [];
    $selectLoginNames [''] =  "vyber - povinné pole" ;
    $allLogin = $loginRepo->find(' 1=1 order by login_name ', [] );
    $loginsArray=[];
    /** @var  LoginInterface $log */
    foreach ($allLogin as $log) {    
        $loginsArray ['loginName'] = $log->getLoginName();               
        $selectLoginNames [ $log->getLoginName() ] =  $log->getLoginName()  ;
    }
  
    
    $selectRoles = [];
    $selectRoles [AuthController::NULL_VALUE_nahradni] =  "" ;
    $roles = $roleRepo->findAll();
        /** @var RoleInterface $role */ 
    foreach ( $roles as $role ) {
        $selectRoles [$role->getRole()] = $role->getRole() ;
    }    
    
    $selecty = [];
    $selecty['selectRoles'] = $selectRoles;
    $selecty['selectLoginNames'] = $selectLoginNames;

    //---------------------------------------------------------       
    // Credentials všechny
    $credentialsEntities = $credentialsRepo->find(' 1=1 order by login_name_fk ', [] );               
    if ($credentialsEntities) {   
            /** @var CredentialsInterface $entity */
            foreach ($credentialsEntities as $entity) {
                      $nu1 = $entity->getRoleFk();
                      $nu2 = $entity->getLoginNameFk();
                
                $credential = $credentialsRepo->get( $entity->getRoleFk() ?? ''    ) ;               
                $credentials[] = [
                    'roleFk' => ($entity->getRoleFk()) ?? AuthController::NULL_VALUE_nahradni , 
                    'selectRoles' =>  $selectRoles,                     
                    'loginNameFk' =>  $entity->getLoginNameFk(),
                    'passwordHash' => $entity->getPasswordHash()                                               
                    ];
            }   
    }
//    else {                
//    }
                                              
  ?>
    
    <div class="ui styled fluid accordion">           
        <div>             
           <b>CREDENTIALS-tabulka</b>                                 
        </div>                          
        ------------------------------------------------------                      
        <div>      
            <?php  if (isset ($credentialsEntities) ) {   ?>                                
                    <span>Login Name | PasswordHash | Role</span>                                 
                <?= $this->repeat(__DIR__.'/credentials.php',  $credentials )  ?>                         
            <?php }   ?>      
                                      
        </div>
    </div>    

