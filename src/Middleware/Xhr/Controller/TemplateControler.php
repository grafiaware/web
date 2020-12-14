<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Middleware\Xhr\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Site\Configuration;

use Model\Entity\MenuItemInterface;
use Model\Entity\PaperAggregate;

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
    HierarchyAggregateRepo, MenuRootRepo, MenuItemRepo
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

    public function papertemplate(ServerRequestInterface $request, $folder) {
        $paperAggregate = new PaperAggregate();
        $paperAggregate->exchangePaperContentsArray([])   //  ['content'=> Message::t('Contents')]
                ->setTemplate($folder)
                ->setHeadline(Message::t('Headline'))
                ->setPerex(Message::t('Perex'))
                ;

        $view = $this->container->get(View::class)
                                ->setTemplate(new PhpTemplate(Configuration::templateControler()['templates.paperFolder']."$folder/template.php"))
                                ->setData([
                                    'paperAggregate' => $paperAggregate,
                                ]);
        return $this->createResponseFromView($request, $view);
    }

    public function authorTemplate(ServerRequestInterface $request, $folder, $name) {
        $filename = Configuration::templateControler()['templates.authorFolder']."$folder/$name.html";
        if (is_readable($filename)) {
            $str = file_get_contents($filename);
        } else {
            $str = '';
        }
        return $this->createResponseFromString($request, $str);
    }

}
