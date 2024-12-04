<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\ViewModelListInterface;

use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\JobTagRepoInterface;
use Events\Model\Entity\JobTagInterface;


use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class JobTagListViewModel extends ViewModelAbstract implements ViewModelListInterface {

    private $status;  
    
    private $jobTagRepo;

    public function __construct(
            StatusViewModelInterface $status,
            JobTagRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->jobTagRepo = $companyRepo;
    }
    
    private function isAdministrator() {
        return $this->status->getUserRole()==RoleEnum::EVENTS_ADMINISTRATOR;
    }
    
    /**
     * Poskytuje kolekci dat (iterovatelnou) pro generování položek - item komponentů..
     * Položky - item komponenty vziknou tak, že ke každé položce datové kolekce bude vygenerována item komponenta z prototypu
     * a této komponentě bude vložena jako data pro renderování položka kolekce dat. 
     * Pozn. To znamená, že jednotlívé item komponenty nepoužijí (a nepotřebují) vlastní view model.
     * 
     * @return iterable
     */
    public function provideItemDataCollection(): iterable {
        $items=[];     
        foreach ($this->jobTagRepo->findAll() as $jobTag) {

            /** @var JobTagInterface $jobTag */
            $items[] = [
                // conditions
                'editable' => $this->isAdministrator(),
                'remove'=> $this->isAdministrator(),   // přidá tlačítko remove do item
                //route
                'componentRouteSegment' => 'events/v1/jobtag',
                'id' => $jobTag->getId(),
                // data
                'fields' => ['tag' =>  $jobTag->getTag()],
                ];
            }
            if ($this->isAdministrator()) {
                $items[] = [
                // conditions
                'editable' => true,    // seznam je editovatelný - zobrazí formulář a tlačítko přidat 
                //route
                'componentRouteSegment' => 'events/v1/jobtag',
                // text
                'addHeadline' => 'Přidej tag',                
                // data

                ];
            }
        return $items;
    }
    
    /**
     * Poskytuje data pro šablonu list - pro šablonu, která obaluje pro jednotlivé položky
     * @return ArrayIterator
     */
    public function getIterator() {
        $array = [         
            'listHeadline'=>'Tagy pracovních pozic', 
            'items' => $this->getArrayCopy()];
        return new ArrayIterator($array);
    }
}
