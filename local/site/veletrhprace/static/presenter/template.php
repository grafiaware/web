<?php
use Events\Middleware\Events\ViewModel\JobViewModel;
use Template\Compiler\TemplateCompilerInterface;

/** @var JobViewModel $jobModel */
$jobModel = $container->get( JobViewModel::class );
$company = $jobModel->getCompanyByName($shortName);
if (isset($company)) {
    foreach ($jobModel->getCompanyJobList($company->getId()) as $job) {
        $jobs[] = array_merge($job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}, 'shortName' => $shortName]);
    }
} else {
    $jobs = [];
}
