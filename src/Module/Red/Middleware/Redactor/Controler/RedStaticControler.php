<?php
namespace Red\Middleware\Redactor\Controler;

use FrontControler\PresentationFrontControlerAbstract;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Template\Compiler\TemplateCompilerInterface;

use Access\AccessPresentationInterface;
use Access\Enum\AccessPresentationEnum;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class RedStaticControler extends PresentationFrontControlerAbstract {

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
