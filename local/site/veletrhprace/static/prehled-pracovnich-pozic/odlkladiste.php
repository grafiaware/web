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
