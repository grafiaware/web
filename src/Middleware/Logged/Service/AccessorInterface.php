<?php

namespace Middleware\Logged\Service;

/**
 *
 * @author pes2704
 */
interface AccessorInterface {
    /**
     * @return bool Přístup povolen.
     */
    public function granted();
}
