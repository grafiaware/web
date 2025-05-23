<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Auth\Model\Repository\RoleRepoInterface;
use Auth\Model\Entity\RoleInterface;

/** @var PhpTemplateRendererInterface $this */

    /** @var RoleRepoInterface $roleRepo */ 
    $roleRepo = $container->get(RoleRepoInterface::class );
    //------------------------------------------------------------------
 
    $allRoles = $roleRepo->findAll();
    $allRolesArray=[];
    /** @var  RoleInterface $role */
    foreach ($allRoles as $role) {               
        $allRolesArray[] =  ['role' => $role->getRole() ];        
    }
             
  ?>

  
    <div class="ui styled fluid accordion">           
           <b>Role uživatelů </b>
        
            <?= $this->repeat(__DIR__.'/role.php',  $allRolesArray)  ?>
            ------ Přidej další roli --------            
                <?= $this->insert( __DIR__.'/role.php' ) ?>                                                                                 
                                      
    </div>

