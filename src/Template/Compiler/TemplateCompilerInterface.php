<?php
namespace Template\Compiler;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface TemplateCompilerInterface {

    const VARNAME_CONTAINER = 'container';


    public function injectTemplateVars(array $templateVariables);
    public function getCompiledContent(ServerRequestInterface $request, $staticName): string;

}
