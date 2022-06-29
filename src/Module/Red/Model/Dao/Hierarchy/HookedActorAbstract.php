<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Dao\Hierarchy;

use Pes\Database\Handler\HandlerInterface;

/**
 * {@inheritdoc}
 *
 * Description of HookedContentActionsAbstract
 * Abstraktní třída HookedContentActionsAbstract nabízí metodu pro kontrolu, zda handler ovládá běžící transakci.
 *
 * @author pes2704
 */
abstract class HookedActorAbstract implements HookedMenuItemActorInterface {

    protected function checkTransaction(HandlerInterface $transactionHandler) {
        if ( ! $transactionHandler->inTransaction()) {
             throw new \LogicException('Metody třídy '. get_called_class().' lze volat pouze v průběhu databázové transakce s hierarchií.');
        }
    }

}
