<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;

use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyRepoInterface;

/** @var PhpTemplateRendererInterface $this */
    /** @var CompanyRepoInterface $companyRepo */
    $companyRepo = $container->get(CompanyRepo::class );
    $companies = $companyRepo->findAll();
    $pStyle = ['style'=>'color: red;'];

    foreach ($companies as $company) {
        $companyId = $company->getId();
        echo Html::p("events/v1/component/company/$companyId", $pStyle);
        echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/component/company/$companyId",
            ]
        );
        echo Html::p("events/v1/component/companyAddress/$companyId", $pStyle);
        echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/component/companyAddress/$companyId",
            ]
        );

        echo Html::p("events/v1/subcomponent/companyContact/$companyId", $pStyle);
        echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/subcomponent/companyContact/$companyId",
            ]
        );

}
