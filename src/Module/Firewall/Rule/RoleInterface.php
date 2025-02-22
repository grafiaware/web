<?php

namespace Firewall\Middleware\Rule;

/**
 *
 * @author pes2704
 */
interface RoleInterface {
    /**
     * @return bool Přístup povolen.
     */
    public function granted();
    
    public function restrictMessage();
}
