<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Database\Hierarchy;

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

    private $usedTransactionHandler;
    private $paramsOK;

    protected function checkTransaction(HandlerInterface $transactionHandler) {
        if ( ! $transactionHandler->inTransaction()) {
             throw new \LogicException('Metody třídy '. get_called_class().' lze volat pouze v průběhu databázové transakce s hierarchií.');
        }
    }

    /**
     *
     * @param HandlerInterface $transactionHandler
     * @return boolean Vrcí vždy TRUE nebo vyhodí výjimku.
     * @throws Exception
     */
    protected function checkParams(HandlerInterface $transactionHandler) {
        if (!$this->paramsOK) {
            $this->usedTransactionHandler = $transactionHandler;
            $transactionHandler->prepare(
                        "SELECT lang_code_fk
                        FROM article_lang
                        WHERE lang_code_fk = :lang");
            $stmt->bindParam(':lang', $this->langCodeFk);
            $stmt->execute();
            if (!$stmt->rowCount()) {
                throw new Exception('Neznámá hodnota lang_code_fk v tabulce article_lang: '.$this->langCodeFk);
            }
            return TRUE;
        }
    }

}
