<?php 
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;
use Events\Model\Entity\RepresentativeInterface;
use Events\Model\Repository\CompanyParameterRepoInterface;
use Events\Model\Repository\CompanyParameterRepo;
use Events\Model\Entity\CompanyParameterInterface;


/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);

if (isset($companyId)) {
    echo Html::tag('div', 
        [
            'class'=>'cascade nazev-firmy',
            'data-red-apiuri'=>"events/v1/data/company/$companyId",
        ]
    );
    echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/company/$companyId/companyinfo",
        ]
    ); 
    echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/company/$companyId/companyaddress",
        ]
    );
    echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/company/$companyId/companycontact",
        ]
    );

}


