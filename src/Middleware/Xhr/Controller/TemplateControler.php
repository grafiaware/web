<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Xhr\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Model\Entity\MenuItemInterface;

// komponenty
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent,
    Flash\FlashComponent
};

use \Middleware\Xhr\AppContext;

####################

use Model\Repository\{
    HierarchyNodeRepo, MenuRootRepo, MenuItemRepo
};

use \StatusManager\StatusPresentationManager;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
//use Pes\View\Recorder\RecorderProvider;
//use Pes\View\Recorder\VariablesUsageRecorder;
//use Pes\View\Recorder\RecordsLogger;
use \Pes\View\ViewFactory;

/**
 * Description of GetControler
 *
 * @author pes2704
 */
class TemplateControler extends XhrControlerAbstract {

    const DEEAULT_HIERARCHY_ROOT_COMPONENT_NAME = 's';

    ### action metody ###############

    public function papertemplate(ServerRequestInterface $request, $templateName) {
        $view = $this->container->get(View::class)
                                ->setTemplate(new PhpTemplate(PROJECT_PATH."public/web/templates/paper/".$templateName."/template.php"))
                                ->setData([
                                    'templateName' => $templateName,
                                    'headline' => Message::t('Headline'),
                                    'perex' => Message::t('Perex'),
                                    'contents' => [ ['content'=> Message::t('Contents')]],
                                ]);
        return $this->createResponseFromView($request, $view);
    }


}
