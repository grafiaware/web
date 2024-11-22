<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Middleware\Events\Controler;

use FrontControler\PresentationFrontControlerAbstract;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Access\AccessPresentationInterface;
use Template\Compiler\TemplateCompilerInterface;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class EventStaticControler extends PresentationFrontControlerAbstract {

    private $templateCompiler;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            AccessPresentationInterface $accessPresentation,
            TemplateCompilerInterface $templateCompiler
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo, $accessPresentation);
        $this->templateCompiler = $templateCompiler;
    }

    ### action metody ###############

    public function static(ServerRequestInterface $request, $staticName) {
        $realName = str_replace('_', '/', $staticName);
        $this->templateCompiler->injectTemplateVars([TemplateCompilerInterface::VARNAME_CONTAINER => $this->container]);
        $compiledContent = $this->templateCompiler->getCompiledContent($request, $realName);
        return $this->createStringOKResponse($compiledContent);
    }


}
