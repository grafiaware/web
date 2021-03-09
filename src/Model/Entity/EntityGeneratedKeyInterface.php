<?php
namespace Model\Entity;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pes2704
 */
interface EntityGeneratedKeyInterface extends EntityInterface {

    const DB_GENERATED = 'DB_GENERATED';
    const DAO_GENERATED = 'DAO_GENERATED';
    const MANUAL_DB_VERIFIED = 'MANUAL_DB_VERIFIED';

    public function hasGeneratedKey(): bool;
    public function getGeneratedKeyType(): string;
}
