<?php
namespace Configuration;

use Configuration\Exception\NoTemplateException;

/**
 * Description of ComponentComfiguration
 *
 * @author pes2704
 */
class ComponentConfiguration implements ComponentConfigurationInterface {

    private $logsDirectory;
    private $logsRender;
    private $templates=[];


    public function __construct(
            $logsDirectory,
            $logsRender,
            array $templates
            ) {
        $this->logsDirectory = $logsDirectory;
        $this->logsRender = $logsRender;
        $this->templates = $templates;
    }

    public function getLogsDirectory() {
        return $this->logsDirectory;
    }

    public function getLogsRender() {
        return $this->logsRender;
    }

    public function getTemplate($name): string {
        if (!array_key_exists($name, $this->templates)) {
            throw new NoTemplateException("V konfiguraci není definována temlpate '$name'.");
        }
        return $this->templates[$name] ?? null;
    }

}
