<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelListInterface extends ViewModelInterface {
    /**
     * Poskytuje iterovatelnou kolekci entit pro generování položek - item komponentů.
     * Položky - item komponenty vzniknou tak, že ke každé entitě bude vygenerována item komponenta z prototypu
     * a této komponentě bude vložena do view modelu položka kolekce entit.
     *  
     * @return iterable
     */
    public function provideItemEntityCollection(): iterable;
    public function isListEditable(): bool;
}
