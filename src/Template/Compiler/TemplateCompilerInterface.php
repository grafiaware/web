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

    public function injectTemplateVars(iterable $templateVariables);
    public function getCompiledContent($templatePath): string;

}
