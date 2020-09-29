<?php
namespace Middleware\Build\Controler;

use Psr\Container\ContainerInterface;

/**
 *
 * @author pes2704
 */
interface BuildControlerInterface {
    public function injectContainer(ContainerInterface $buildContainer): BuildControlerInterface;
    public static function timeout();
}
