<?php
namespace Build\Middleware\Build\Controller;

use Psr\Container\ContainerInterface;

/**
 *
 * @author pes2704
 */
interface BuildControllerInterface {
    public function injectContainer(ContainerInterface $buildContainer): BuildControllerInterface;
    public static function timeout();
}
