<?php
namespace Web\Component\ViewModel\Content\Authored\Multipage;

use Web\Component\ViewModel\Content\Authored\AuthoredViewModelInterface;

use Red\Model\Entity\MultipageInterface;
use Red\Model\Entity\HierarchyAggregateInterface;

/**
 *
 * @author pes2704
 */
interface MultipageViewModelInterface extends AuthoredViewModelInterface {

    /**
     * Vrací Multipage, pokud existuje a menu item je aktivní (zveřejněný) nebo prezentace je v editačním režimu.
     *
     * @return MultipageInterface|null
     */
    public function getMultipage(): ?MultipageInterface;

    /**
     * @return HierarchyAggregateInterface[]
     */
    public function getSubTree();
}
