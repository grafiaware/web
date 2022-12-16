<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\RepresentativeRepo;

use Events\Model\Entity\CompanyInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Events\Model\Entity\CompanyContactInterface;
use Events\Model\Entity\RepresentativaInterface;


/** @var PhpTemplateRendererInterface $this */




    /** @var RepresentativeRepo $representativeRepo */
    $representativeRepo = $container->get(RepresentativeRepo::class );
    /** @var CompanyRepo $companyRepo */ 
    $companyRepo = $container->get(CompanyRepo::class );
    /** @var CompanyContactRepo $companyContactRepo */
    $companyContactRepo = $container->get(CompanyContactRepo::class );
    /** @var CompanyAddressRepo $companyAddressRepo */ 
    $companyAddressRepo = $container->get(CompanyAddressRepo::class );
    //------------------------------------------------------------------
                    
        $companiesContacts[];       
         /**  @var CompanyInterface $company */             
        $companyEntities = $companyRepo->findAll();
        
        foreach ($companyEntities as  $company ){            
            $companyContactEntities = $companyContactRepo->find( " company_id = :id ",  ['id'=> $company->getId()] );           
            
            $companyContacts=[];
            /**  @var CompanyContactInterface $cntct */
            foreach ($companyContactEntities as $cntct) {
                /** @var CompanyContactInterface $cntct */
                $companyContacts[] = [
                    'companyContactId' => $cntct->getId(),
                    'companyId' => $cntct->getCompanyId(),
                    'name' =>  $cntct->getName(),
                    'phones' =>  $cntct->getPhones(),
                    'mobiles' =>  $cntct->getMobiles(),
                    'emails' =>  $cntct->getEmails()
                    ];
            }
            $companiesContacts[] = $companyContacts;

        }  


?>

            

           


       $readonly = '';
        $disabled = '';
?>
                 
                <form class="ui huge form" action="" method="POST" >
                    <input type='hidden' name="company-id" value="<?= isset($companyId)? $companyId : '' ?>" >
                    <input type='hidden' name="company-contact-id" value="<?= isset($companyContactId)? $companyContactId : '' ?>" >
                    
                    <div class="two fields">                        
                        <div class="field">
                        <label>Jméno kontaktu</label>
                            <input <?= $readonly ?> type="text" name="name" placeholder="" maxlength="90" value="<?= isset($name)?  $name : '' ?>">
                         </div>  
                        <div class="field">
                            <label>E-maily</label>
                            <input <?= $readonly ?> type="email" name="emails" placeholder="" maxlength="90" value="<?= isset($emails)?  $emails : ''  ?>">
                        </div>
                    </div>
                    <div class="two fields">
                        <div class="field">
                            <label>Telefony</label>
                            <input <?= $readonly ?> type="text" name="phones" placeholder="" maxlength="90" value="<?= isset($phones)?  $phones : '' ?>">
                        </div>
                        <div class="field">
                            <label>Mobily</label>
                            <input <?= $readonly ?> type="text" name="mobiles" placeholder="" maxlength="90" value="<?= isset($mobiles)?  $mobiles : '' ?>">
                        </div>
                    </div>                 
                    
                        <?php
                        if($readonly === '') {
                        ?>
                        <div>
                            <?=
                             isset($companyContactId) ?
                            "<button class='ui primary button' type='submit' formaction='events/v1/companycontact/". $companyContactId ."' > Uložit </button>" :
                            "<button class='ui primary button' type='submit' formaction='events/v1/companycontact' > Uložit </button>" ;
                            ?>                                                                                                         
                      <!--  </div>
                        <div> -->
                            <?=
                             isset($companyContactId) ?
                            "<button class='ui primary button' type='submit' formaction='events/v1/companycontact/". $companyContactId ."/remove' > Odstranit kontakt </button>" :
                            "" ;
                            ?>                                                                                                         
                        </div>
                        <?php
                        }
                        ?>
                    
                </form>           