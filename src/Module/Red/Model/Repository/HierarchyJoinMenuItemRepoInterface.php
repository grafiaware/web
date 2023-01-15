<?php
namespace Red\Model\Repository;

use Model\Repository\RepoAssotiatingOneInterface;
use Red\Model\Entity\HierarchyAggregateInterface;

/**
 *
 * @author pes2704
 */
interface HierarchyJoinMenuItemRepoInterface extends RepoAssotiatingOneInterface {

    /**
     *
     * @param type $langCode
     * @param type $uid
     * @return HierarchyAggregateInterface|null
     */
    public function get($langCodeFk, $uidFk): ?HierarchyAggregateInterface;

    /**
     * JEN POMOCNÁ METODA PRO LADĚNÍ
     * Vrací item podle hodnoty lang_code a title v tabulce menu_item.
     * Vybírá case insensitive. Pokud je více položek se stejným title, vrací jen první.
     *
     * @param string $langCode Identifikátor language
     * @param string $title
     * @return HierarchyAggregateInterface|null
     */
    public function getNodeByTitle($key): ?HierarchyAggregateInterface;

    /**
     * Vrací pole položek "dětí", tj. přímých potomků vyhledanou podle rodiče - primárního (kompozitního) klíče: (langCode, uid) s podmínkou active a actual.
     * Podud je parametr active TRUE, vybírá jen položky s vlastností active=TRUE, jinak vrací aktivní i neaktivní.
     * Pokud je parametr actual=TRUE, vybírá jen položky kde dnešní datum je mezi show_time a hide_time včetně.
     *
     * @param string $langCode Identifikátor language
     * @param string $parentUid Identifikátor rodiče z menu_nested_set
     * @return HierarchyAggregateInterface array of
     */
    public function findChildrenNodes($langCode, $parentUid);

    /**
     *
     * @param string $langCode Identifikátor language
     * @return HierarchyAggregateInterface array of
     */
    public function getFullTree($langCode);

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $rootUid
     * @param int $maxDepth int or NULL
     * @return HierarchyAggregateInterface array of
     */
    public function getSubTree($langCode, $rootUid, $maxDepth=NULL);

}
