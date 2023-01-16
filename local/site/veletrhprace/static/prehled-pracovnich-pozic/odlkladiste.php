<?php

<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>
        <content class='prehled-pozic'>
           <?=  $this->repeat(__DIR__.'/content/presenter-jobs.php', $allJobs);  ?>
        </content>
    </section>
</article>
<article class="paper">
    <section>
        <headline>
            <?php include "headline.php" ?>
        </headline>
        <perex>
            <?php include "perex.php" ?>
        </perex>
    </section>
    <section>
        <content class='prehled-pozic'>
           <?=  $this->repeat(__DIR__.'/content/presenter-jobs.php', $allJobsI);  ?>
        </content>
    </section>
</article>


                <?= $this->insert( ConfigurationCache::componentController()['templates']."paper/presenter-job/content/vypis-pozic.php", $presenterJobs); ?>


                <select <?= $disabled ?>>
                    <?php
                    /** @var CompanyInterface $compI */
                    foreach ($presenterModel->getCompanyListI() as $shortN => $compI) {
                    ?>
                    <option value="<?= $compI->getName() ?>" <?= $shortN==$representativePersonI['shortName'] ? "selected" : "" ?> > <?= $compI->getName() ?></option>
                    <?php
                    }
                    ?>                   
                </select>

19.10.22
  <?=  $this->repeat(__DIR__.'/company-contacts/company-contacts.php', $companyContactsI  )  ?>


            <div class="active title">
                <i class="dropdown icon"></i>
                Kontakty vystavovatele
            </div>                        
            <div class="active content">      
                <?= $this->repeat(__DIR__.'/company-contact.php',  $contacts  )  ?>

                <div class="active title">
                    <i class="dropdown icon"></i>
                    Přidej další kontakt vystavovatele
                </div>  
                <div class="active content">     
                    <?= $this->insert( __DIR__.'/company-contact.php', [ 'companyId' => $idCompany ] ) ?>                                                                                 
                </div>                  
            </div>        



<?= "<button class='ui primary button' type='submit' 
                                        formaction='events/v1/sendjobrequest/" . $visitorLoginName. "/" . $jobId . " >
                                        Odeslat mailem na " . $presenterEmail . "></button>"  ?>