<?php
namespace Auth\Middleware\Login\Controller;

use Access\Enum\RoleEnum;
use Access\Enum\AllowedActionEnum;
use FrontControler\PresentationFrontControlerAbstract;
use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Template\Compiler\TemplateCompilerInterface;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of ComponentController
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
            RoleEnum::SUPERVISOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::EDITOR => [AllowedActionEnum::GET => self::class, AllowedActionEnum::POST => self::class],
            RoleEnum::AUTHENTICATED => [AllowedActionEnum::GET => self::class],
            RoleEnum::ANONYMOUS => [AllowedActionEnum::GET => self::class]
        ];
    }
    
    ### action metody ###############

    public function static(ServerRequestInterface $request, $staticName) {
        if($this->isAllowed(AllowedActionEnum::GET)) {
            $realName = str_replace('_', '/', $staticName);
            $this->templateCompiler->injectTemplateVars([TemplateCompilerInterface::VARNAME_CONTAINER => $this->container]);
            $compiledContent = $this->templateCompiler->getCompiledContent($request, $realName);
            return $this->createResponseFromString($request, $compiledContent);
        } else {
            return $this->createResponseFromView($request, $this->getNonPermittedContentView());
        }
    }


}
