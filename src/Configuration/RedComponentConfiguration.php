<?php
namespace Configuration;

/**
 * Description of ComponentComfiguration
 *
 * @author pes2704
 */
class RedComponentConfiguration implements ComponentConfigurationInterface {
//            'component.logs.directory' => 'Logs/App/Red',
//            'component.logs.render' => 'Render.log',

    private $logsDirectory;
    private $logsRender;

    public function __construct(
            $logsDirectory,
            $logsRender
            ) {
        $this->logsDirectory = $logsDirectory;
        $this->logsRender = $logsRender;
    }

    public function getLogsDirectory() {
        return $this->logsDirectory;
    }

    public function getLogsRender() {
        return $this->logsRender;
    }

}
