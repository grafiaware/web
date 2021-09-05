<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\EntityAbstract;

use Auth\Model\Entity\LoginAggregateFullInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class StatusSecurity extends EntityAbstract implements StatusSecurityInterface {

    /**
     * @var LoginAggregateFullInterface
     */
    private $loginAggregate;

    /**
     * Vrací jméno
     *
     * @return LoginAggregateFullInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateFullInterface {
        return $this->loginAggregate;
    }


    /**
     * {@inheritdoc}
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function renewSecurityStatus(LoginAggregateFullInterface $loginAggregate=null): StatusSecurityInterface {
        $this->loginAggregate = $loginAggregate;
        return $this;
    }

    public function hasSecurityContext(): bool {
        return isset($this->loginAggregate) AND $this->loginAggregate->isPersisted();
    }
}
