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