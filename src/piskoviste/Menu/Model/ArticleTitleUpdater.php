<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Menu\Model\HierarchyHooks;

use Pes\Database\Handler\HandlerInterface;

/**
 * Description of ArticleTitleUpdater
 *
 * @author pes2704
 */
class ArticleTitleUpdater {

    private $databaseHandler;

    public function __construct(HandlerInterface $databaseHandler) {
        $this->databaseHandler = $databaseHandler;
    }
    
    /**
     *
     * @param string $lang_code_fk
     * @param string $uid_fk
     * @param string $title
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function updateTitle($lang_code_fk, $uid_fk, $title) {
        $stmt = $this->databaseHandler->prepare(
                "UPDATE article
                SET title = :title
                WHERE lang_code_fk = :lang_code_fk AND uid_fk = :uid_fk");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':lang_code_fk', $lang_code_fk);
        $stmt->bindValue(':uid_fk', $uid_fk);
        return $stmt->execute();
    }
}
