<?php
use Events\Middleware\Events\ViewModel\JobViewModel;
use Template\Compiler\TemplateCompilerInterface;

/** @var JobViewModel $jobModel */
$jobModel = $container->get( JobViewModel::class );
$company = $jobModel->getCompanyByName($companyName);
if (isset($company)) {
    foreach ($jobModel->getCompanyJobList($company->getId()) as $job) {
        //TODO: restrukturovat - jobs 3 položky-> pole jobs, položka containe, položka companyName
        $jobs[] = array_merge($job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}, 'companyName' => $companyName]);
    }
} else {
    $jobs = [];
}
