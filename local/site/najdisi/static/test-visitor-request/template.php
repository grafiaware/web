<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Events\Model\Repository\DocumentRepo;
use Events\Model\Repository\DocumentRepoInterface;
use Access\Enum\RoleEnum;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

    /** @var StatusViewModelInterface $statusViewModel */
    $statusViewModel = $container->get(StatusViewModel::class);
    $role = $statusViewModel->getUserRole();
    $loginName = $statusViewModel->getUserLoginName();

    $isVisitor = (isset($role) AND $role==RoleEnum::VISITOR);

    /** @var DocumentRepoInterface $documentRepo */
    $documentRepo = $container->get(DocumentRepo::class );
    echo "Jen pro admina!";
    echo Html::p("Přehled job requestů");
    echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/visitorjobrequest"
        ]
    );
    echo "Jen pro návštěvníka!";
    echo Html::p("Job request");
    if ($isVisitor) {
    echo Html::tag('div', 
            [
                'class'=>'cascade',
                'data-red-apiuri'=>"events/v1/data/visitorjobrequest/$loginName"
            ]
        );
    }    