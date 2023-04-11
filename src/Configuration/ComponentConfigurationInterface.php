<?php
namespace Configuration;


/**
 *
 * @author pes2704
 */
interface ComponentConfigurationInterface {

    public function getLogsDirectory();

    public function getLogsRender();

    public function getTemplate($name): string;
}
