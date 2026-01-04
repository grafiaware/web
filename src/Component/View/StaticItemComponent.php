<?php

namespace Component\View;

use Component\View\ComponentCompositeAbstract;
use Component\ViewModel\StaticItemViewModel;

use Access\Enum\RoleEnum;
use Access\Enum\AccessPresentationEnum;
use Configuration\ComponentConfigurationInterface;
use Template\Compiler\TemplateCompilerInterface;
use Pes\View\Template\PhpTemplate;
/**
 * Description of ItemTypeSelectComponent
 *
 * @author pes2704
 */
class StaticItemComponent extends ComponentCompositeAbstract implements StaticItemComponentInterface {

    private $templateCompiler;
    
    /**
     * @var StaticItemViewModel
     */
    protected $contextData;
    
    public static function getComponentPermissions(): array {
        return [
            RoleEnum::SUPERVISOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::EDITOR => [AccessPresentationEnum::DISPLAY => true, AccessPresentationEnum::EDIT => true],
            RoleEnum::AUTHENTICATED => [AccessPresentationEnum::DISPLAY => true],
            RoleEnum::ANONYMOUS => [AccessPresentationEnum::DISPLAY => true],
        ];
    }
    
    /**
     * Nastaví pro renderování PHP template se jménem souboru (šablony), Jm=no souboru získá z ViewModelu.
     * Dta pro renderování tedy jsou jméno souboru s template.
     * 
     * @return void
     */
//    public function beforeRenderingHook(): void {        
//        $templateFilename = $this->contextData->getStaticTemplatePath();
//        $this->setTemplate(new PhpTemplate($templateFilename));
//    }
    
    public function static(ServerRequestInterface $request, $menuItemId) {
        
        
        
        $realName = str_replace('_', '/', $staticName);
        $this->templateCompiler->injectTemplateVars([TemplateCompilerInterface::VARNAME_CONTAINER => $this->container]);
        $compiledContent = $this->templateCompiler->getCompiledContent($realName);
        return $this->createStringOKResponse($compiledContent);
    }    
}
