<?php
namespace Database\Hierarchy;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of HierarchyAbstract
 *
 * @author pes2704
 */
abstract class HierarchyAbstract {

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     *
     * @param HandlerInterface $handler Databázový handler
     */
    public function __construct(HandlerInterface $handler) {
        $this->handler = $handler;
    }

}
