<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Middleware\Redactor\Controler;

use Psr\Http\Message\ServerRequestInterface;

use Site\ConfigurationCache;
use Configuration\TemplateControlerConfigurationInterface;

use FrontControler\FrontControlerAbstract;
use FrontControler\FrontControlerInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\PaperAggregatePaperSection;

// view modely
use Red\Component\ViewModel\Content\Authored\Paper\PaperViewModel;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageViewModel;
use Red\Component\ViewModel\Content\Authored\Paper\PaperTemplatePreviewViewModel;
use Red\Component\ViewModel\Content\Authored\Paper\PaperTemplatePreviewViewModelInterface;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageTemplatePreviewViewModel;
use Red\Component\ViewModel\Content\Authored\Multipage\MultipageTemplatePreviewViewModelInterface;

// komponenty
use Red\Component\View\Content\Authored\AuthoredComponentInterface;
use Red\Component\View\Content\Authored\Paper\PaperTemplatePreviewComponent;
use Red\Component\View\Content\Authored\Multipage\MultipageTemplatePreviewComponent;

use Template\Seeker\Exception\TemplateServiceExceptionInterface;

####################
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Template\Seeker\TemplateSeekerInterface;
use Red\Model\Enum\AuthoredTemplateTypeEnum;

use Pes\Text\Message;

####################
//use Pes\Debug\Timer;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use \View\Includer;

use Pes\View\Template\ImplodeTemplate;

/**
 * Description of GetController
 *
 * @author pes2704
 */
class TemplateControler_2 extends FrontControlerAbstract {

    /**
     *
     * @var TemplateSeekerInterface
     */
//    private $templateSeeker;

    /**
     * 
     * @var TemplateControlerConfigurationInterface
     */
//    private $configuration;
    
    /**
     * Kontroler pro obsluhu požadavků tinyMce.
     *
     * Metody odesílají obsah šablon pro TinyMce.
     *
     * @param StatusSecurityRepo $statusSecurityRepo
     * @param StatusFlashRepo $statusFlashRepo
     * @param StatusPresentationRepo $statusPresentationRepo
     * @param TemplateSeekerInterface $templateSeeker
     */
    public function __construct(
            StatusSecurityRepo $statusSecurityRepo, 
            StatusFlashRepo $statusFlashRepo, 
            StatusPresentationRepo $statusPresentationRepo,
            TemplateSeekerInterface $templateSeeker,
            TemplateControlerConfigurationInterface $configuration
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->templateSeeker = $templateSeeker;
        $this->configuration = $configuration;
    }
}

