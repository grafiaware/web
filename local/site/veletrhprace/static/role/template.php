<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

//use Auth\Model\Repository\CredentialsRepoInterface;
//use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\RoleRepoInterface;
//use Auth\Model\Repository\RoleRepo;
//use Auth\Model\Repository\LoginRepoInterface;

use Auth\Model\Entity\RoleInterface;
//use Auth\Model\Entity\CredentialsInterface;

//use Auth\Middleware\Login\Controller\AuthController;

/** @var PhpTemplateRendererInterface $this */

    /** @var RoleRepoInterface $roleRepo */ 
    $roleRepo = $container->get(RoleRepoInterface::class );
    //------------------------------------------------------------------
 
    $allRoles = $roleRepo->findAll();
    $allRolesArray=[];
    //$allRolesString=[]; 
    /** @var  RoleInterface $role */
    foreach ($allRoles as $role) {         
        $allRolesArray[$role->getRole()] = $role->getRole();
       // $tg ['tagId'] = $role->getId();               
       // $allRolesArray[] = $rl;       
        //$allTagsString[] = $tag->getTag();
    }
             
  ?>

    
    <div class="ui styled fluid accordion">   
        
        <div>                
           <b>Role uživatelů </b>
        </div>                           
        ------------------------------------------------------        
        
         <div>      
            <?= $this->repeat(__DIR__.'/role.php',  $allRolesArray)  ?>
            <div>       
                ------ Přidej další roli --------
            </div>  
            <div>     
                <?= $this->insert( __DIR__.'/role.php' ) ?>                                                                                 
            </div>                  
        </div>           
                                      
    </div>

