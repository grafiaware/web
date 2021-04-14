<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Site\Configuration;

use Model\Repository\StatusSecurityRepo;
use Model\Entity\LoginAggregateCredentialsInterface;;
use Model\Entity\CredentialsInterface;

use Model\Repository\EnrollRepo;
use Model\Arraymodel\Job;
use Model\Arraymodel\Presenter;

        $readonly = 'readonly="1"';
        $disabled = 'disabled="1"';
//        $readonly = '';
//        $disabled = '';

$isPresenter = false;

$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurity = $statusSecurityRepo->get();
/** @var LoginAggregateCredentialsInterface $loginAggregate */
$loginAggregate = $statusSecurity->getLoginAggregate();

$jobModel = new Job();

if (isset($loginAggregate)) {
    $credentials = $loginAggregate->getCredentials();
    $role = $credentials->getRole();
    $loginName = $loginAggregate->getLoginName();
    $presenterModel = new Presenter();
    $presenterPerson = $presenterModel->getPerson($loginName);

    if(isset($role) AND $role==Configuration::loginLogoutControler()['rolePresenter']) {
        $isPresenter = true;

        $presenterJobs = array();
        $shortName = $presenterPerson['shortName'];  // každý s rolí presenter musí existovat v modelu jako presenterPerson
        foreach ($jobModel->getCompanyJobList($shortName) as $job) {
            $jobs[] = array_merge($job, ['container' => $container, 'shortName' => $shortName]);
        }
    }
}


/** @var EnrollRepo $enrollRepo */
$enrollRepo = $container->get(EnrollRepo::class);
$enrolls = $enrollRepo->findAll();
$eventCountById = [];
foreach ($enrolls as $enroll) {
    if (array_key_exists($enroll->getEventid(), $eventCountById)) {
        $eventCountById[$enroll->getEventid()]++;
    } else {
        $eventCountById[$enroll->getEventid()] = 1;
    }
}

if($isPresenter) {
    $headline = "Profil vystavovatele";
    $perex = $loginAggregate->getLoginName();
    ?>
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
            <div class="field margin">
                <label>Společnost</label>
                <select <?= $disabled ?>>
                    <?php
                    foreach ($presenterModel->getCompanyList() as $shortN => $comp) {
                    ?>
                    <option value="<?= $comp['name']?>" <?= $shortN==$presenterPerson['shortName'] ? "selected" : "" ?> > <?= $comp['name']?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
                <?php include "content/profil.php" ?> <!-- Tiny pro .working-data -->
        </section>
    </article>
    <?php
} else {
    $headline = "Profil vystavovatele";
    $perex = "Profil vystavovatele je dostupný pouze přihlášenému vystavovateli.";
    ?>
    <article class="paper">
        <section>
            <headline>
                <?php include "headline.php" ?>
            </headline>
            <perex>
                <?php include "perex.php" ?>
            </perex>
        </section>
    </article>
    <?php
}

