<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyListAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;
use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;
use Events\Model\Repository\PozadovaneVzdelaniRepoInterface;
use Events\Model\Entity\JobInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;
use LogicException;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyJobListViewModel extends ViewModelFamilyListAbstract {
    
    private $status;
    private $companyRepo;
    private $jobRepo;
    private $pozadovaneVzdelaniRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            JobRepoInterface $jobRepo,
            PozadovaneVzdelaniRepoInterface $pozadovaneVzdelaniRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->jobRepo = $jobRepo;
        $this->pozadovaneVzdelaniRepo = $pozadovaneVzdelaniRepo;    
    }    
    
    use RepresentativeTrait;    

    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyEditor($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    private function selectEducations() {
        $selectEducations = [];
        $selectEducations [''] =  "vyber - povinné pole" ;
        $vzdelaniEntities = $this->pozadovaneVzdelaniRepo->findAll();
        /** @var PozadovaneVzdelaniInterface $vzdelaniEntity */ 
        foreach ( $vzdelaniEntities as $vzdelaniEntity ) {
            $selectEducations [$vzdelaniEntity->getStupen()] =  $vzdelaniEntity->getVzdelani() ;
        }           
        return $selectEducations;
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
        $componentRouteSegment = "events/v1/".$this->getFamilyRouteSegment();
        $parentId = $this->getParentId();

        $companyJobs = $this->jobRepo->find( " company_id = :companyId ",  ['companyId'=> $parentId ] );
        $selectEducations = $this->selectEducations();        
        
        $isAdministrator = $this->isAdministrator();
        $editableItem = $this->isAdministrator() || $this->isCompanyEditor($companyId);        
        
        $items=[];     
        foreach ($companyJobs as $job) {
            /** @var JobInterface $job */
            $items[] = [
                // conditions
                'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
                'remove'=> $isAdministrator,   // přidá tlačítko remove do item
                //route
                'componentRouteSegment' => $componentRouteSegment,
                'id' => $job->getId(),
                // data
                'fields' => [
                    'editable' => $editableItem,
                    'selectEducations' =>  $selectEducations,
                    'pozadovaneVzdelaniStupen' =>  $job->getPozadovaneVzdelaniStupen(),
                    'nazev' =>  $job->getNazev(),                
                    'mistoVykonu' =>  $job->getMistoVykonu(),
                    'popisPozice' =>  $job->getPopisPozice(),
                    'pozadujeme' =>  $job->getPozadujeme(),
                    'nabizime' =>  $job->getNabizime(),                    
                    ],   
                ];
        }
        if ($isAdministrator) {  // přidání item pro přidání společnosti
            $items[] = [
                // conditions
                'editable' => true,    // seznam je editovatelný - zobrazí formulář a tlačítko přidat 
                // text
                'addHeadline' => 'Přidej pozici',
                //route
                'componentRouteSegment' => $componentRouteSegment,                
                // data
                'fields' => [
                    'editable' => $editableItem,
                    'selectEducations' =>  $selectEducations,
                    ],
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
            'listHeadline'=>'Pozice', 
            'items' => $this->getArrayCopy()];
        $this->appendData($array);
        return parent::getIterator();     }
}
