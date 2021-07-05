<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Component\Controller;

use Psr\Http\Message\ServerRequestInterface;

use Site\Configuration;

use FrontController\PresentationFrontControllerAbstract;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\PaperAggregatePaperContent;

// komponenty
use Component\View\{
    Generated\LanguageSelectComponent,
    Generated\SearchPhraseComponent,
    Generated\SearchResultComponent,
    Generated\ItemTypeSelectComponent,
    Flash\FlashComponent
};

####################

use Red\Model\Repository\{
    HierarchyAggregateRepo, MenuRootRepo, MenuItemRepo
};

use \StatusManager\StatusPresentationManager;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;
use \View\Includer;
/**
 * Description of GetController
 *
 * @author pes2704
 */
class TemplateControler extends PresentationFrontControllerAbstract {

    ### action metody ###############

    /**
     * Vrací obsah šablony.
     * @param ServerRequestInterface $request
     * @param type $templateName
     * @return type
     */
    public function articletemplate(ServerRequestInterface $request, $templateName) {
//        1 pokus - sůožka v site, kdy není readable -> 2. pokus složka v common
//                ?? více složek (v Comfiguration pole) postupně prohledávaných
        $filename = $this->seekTemplate(Configuration::templateController()['templates.articleFolder'], $templateName);
        if ($filename) {
            $str = (new Includer())->protectedIncludeScope($filename);
//            $str = file_get_contents($filename);
            $this->statusPresentationRepo->get()->setLastTemplateName($templateName);
        } else {
            $this->statusPresentationRepo->get()->setLastTemplateName('');
            $str = '';
        }
        return $this->createResponseFromString($request, $str);
    }

    public function papertemplate(ServerRequestInterface $request, $folder) {
        $paperAggregate = new PaperAggregatePaperContent();
        $paperAggregate->exchangePaperContentsArray([])   //  ['content'=> Message::t('Contents')]
                ->setTemplate($folder)
                ->setHeadline(Message::t('Headline'))
                ->setPerex(Message::t('Perex'))
                ;

        $view = $this->container->get(View::class)
                                ->setTemplate(new PhpTemplate(Configuration::templateController()['templates.paperFolder']."$folder/template.php"))
                                ->setData([
                                    'paperAggregate' => $paperAggregate,
                                ]);
        return $this->createResponseFromView($request, $view);
    }

    public function authorTemplate(ServerRequestInterface $request, $folder, $name) {
        $filename = Configuration::templateController()['templates.authorFolder']."$folder/$name.html";
        if (is_readable($filename)) {
            $str = file_get_contents($filename);
        } else {
            $str = '';
        }
        return $this->createResponseFromString($request, $str);
    }


    #### private ######################

    private function seekTemplate($templatesFolders, $templateName) {
        foreach ($templatesFolders as $templatesFolder) {
            $filename = $templatesFolder."$templateName/template.php";
            if (is_readable($filename)) {
                return $filename;
            }
        }
        return false;
    }
}
