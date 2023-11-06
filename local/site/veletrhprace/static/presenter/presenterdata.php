<?php
use Status\Model\Repository\StatusSecurityRepo;

use Events\Middleware\Events\ViewModel\JobViewModel;
use Template\Compiler\TemplateCompilerInterface;

#####################
# vyžaduje proměnnou $companyName s hodnotou, která se nachází v db events, tabulce company, ve sloupci name
#####################


/** @var StatusSecurityRepo $statusSecurityRepo */
$statusSecurityRepo = $container->get(StatusSecurityRepo::class);
$statusSecurity = $statusSecurityRepo->get();
$jobs = [];
if ($companyName) {
    /** @var JobViewModel $jobModel */
    $jobModel = $container->get( JobViewModel::class );
    $company = $jobModel->getCompanyByName($companyName);
    if (isset($company)) {
        //TODO PROVIZORNÍ ŘEŠENÍ - jen pro vývoj - nastavení company, kterou uživatel s rolí representative reprezentuje, musí být ve speciální komponentě ((??userAction)
        // tato informace je nezbytná pro ověřování, zda přihlášený uživatel je reprezentantem zobrazované společnosti
        $statusSecurity->setInfo('companyName', $companyName);  // použije se companyName z data.php
        
        foreach ($jobModel->getCompanyJobList($company->getId()) as $job) {
            //TODO: restrukturovat - jobs 3 položky-> pole jobs, položka containe, položka companyName
            $jobs[] = array_merge($job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}, 'companyName' => $companyName]);
        }
    }
} else {
    $statusSecurity->setInfo('companyName', null);
}
