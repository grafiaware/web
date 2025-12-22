<?php
namespace Template\Compiler;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface TemplateCompilerInterface {

    const VARNAME_CONTAINER = 'container';
    const TEMPLATE_FILE_NAME = "/template.php";

    public function injectTemplateVars(array $templateVariables);
    public function getCompiledContent(ServerRequestInterface $request, $templatePath): string;

}
