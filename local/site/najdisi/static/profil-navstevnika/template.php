<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
use Access\Enum\RoleEnum;

use Component\ViewModel\StatusViewModelInterface;
use Component\ViewModel\StatusViewModel;

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$role = $statusViewModel->getUserRole();
$loginName = $statusViewModel->getUserLoginName();

$isVisitor = (isset($role) AND $role==RoleEnum::VISITOR);

//echo Html::tag('div', 
//        [
//            'class'=>'cascade',
//            'data-red-apiuri'=>"events/v1/data/document/xx",
//        ]
//    );


if ($isVisitor) {
echo Html::tag('div', 
        [
            'class'=>'cascade',
            'data-red-apiuri'=>"events/v1/data/visitorprofile/$loginName"
        ]
    );
} else {
    echo Html::tag('div', ["class"=>"ui blue segment"],
                    Html::p("Stránka je určena pouze přihlášenému návštěvníkovi.")
                            .
                '<i class="exclamation icon"></i>Přihlaste se jako návštěvník. <i class="user icon"></i> Přihlášení návštěvníci mohou posílat žádosti přímo zaměstnavateli. Pokud ještě nejste zaregistrování, nejprve se registrujte. <i class="address card icon"></i>'
            );

    
}