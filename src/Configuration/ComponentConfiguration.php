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
    private $logsType;
    private $templates=[];


    public function __construct(
            $logsDirectory,
            $logsRender,
            $logsType,
            array $templates
            ) {
        $this->logsDirectory = $logsDirectory;
        $this->logsRender = $logsRender;
        $this->logsType = $logsType;
        $this->templates = $templates;
    }

    public function getLogsDirectory() {
        return $this->logsDirectory;
    }

    public function getLogsRender() {
        return $this->logsRender;
    }
    
    public function getLogsType() {
        return $this->logsType;
    }

    public function getTemplate($name): string {
        if (!array_key_exists($name, $this->templates)) {
            throw new NoTemplateException("V konfiguraci komponent není definována template '$name'.");
        }
        return $this->templates[$name] ?? null;
    }

}
