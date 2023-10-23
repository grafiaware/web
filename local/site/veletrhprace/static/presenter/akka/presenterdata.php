<?php
use Events\Model\Arraymodel\JobViewModel;
use Template\Compiler\TemplateCompilerInterface;

/** @var JobViewModel $jobModel */
$jobModel = $container->get( JobViewModel::class );
foreach ($jobModel->getCompanyJobList($shortName) as $job) {
    $jobs[] = array_merge($job, ['container' => ${TemplateCompilerInterface::VARNAME_CONTAINER}, 'shortName' => $shortName]);
}

