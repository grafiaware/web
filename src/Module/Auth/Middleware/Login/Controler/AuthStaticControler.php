<?php
namespace Auth\Middleware\Login\Controler;

use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;
use FrontControler\PresentationFrontControlerAbstract;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Template\Compiler\TemplateCompilerInterface;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class AuthStaticControler extends PresentationFrontControlerAbstract {

    private $templateCompiler;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            TemplateCompilerInterface $templateCompiler
            ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->templateCompiler = $templateCompiler;
    }
    
    protected function getActionPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessActionEnum::GET => self::class, AccessActionEnum::POST => true],
            RoleEnum::EDITOR => [AccessActionEnum::GET => self::class, AccessActionEnum::POST => true],
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### action metody ###############

    public function static(ServerRequestInterface $request, $staticName) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            $realName = str_replace('_', '/', $staticName);
            $this->templateCompiler->injectTemplateVars([TemplateCompilerInterface::VARNAME_CONTAINER => $this->container]);
            $compiledContent = $this->templateCompiler->getCompiledContent($request, $realName);
            return $this->createStringOKResponse($compiledContent);
        } else {
            return $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
    }


}
