<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\DocumentInterface;

/**
 *
 * @author pes2704
 */
interface DocumentRepoInterface extends RepoInterface {
    /**
     *
     * @param type $id
     * @return DocumentInterface|null
     */
    public function get($id): ?DocumentInterface;
    
    
    /**
     * 
     * @param type $whereClause
     * @param type $touplesToBind
     * @return DocumentInterface[]
     */
    public function find($whereClause=null, $touplesToBind=[]) : array ;
    
     /**
     * 
     * @return DocumentInterface[]
     */
    public function findAll() : array  ;
    
    /**
     * 
     * @param DocumentInterface $document
     * @return void
     */
    public function add(DocumentInterface $document) :void;
    
    /**
     * 
     * @param DocumentInterface $document
     * @return void
     */
    public function remove(DocumentInterface $document);
}
