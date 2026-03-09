<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyMultiAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanytoNetworkRepoInterface;
use Events\Model\Repository\NetworkRepoInterface;

use Events\Model\Entity\NetworkInterface;
use Events\Model\Entity\CompanytoNetwork;

use Component\ViewModel\FamilyInterface;
use Access\Enum\RoleEnum;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyNetworkMultiViewModel extends ViewModelFamilyMultiAbstract {
    

    private $status;       
    private $companyRepo;
    private $companyToNetworkRepo;
    private $networkRepo;

    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanytoNetworkRepoInterface $companyToNetworkRepo,
            NetworkRepoInterface $networkRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyToNetworkRepo = $companyToNetworkRepo;
        $this->networkRepo = $networkRepo;
    }
    
    use RepresentativeTrait;
    
    public function isMultiEditable(): bool {
        $company = $this->companyRepo->get($this->getFamilyRouteSegment()->getParentId());
        return $this->isCompanyEditor($company->getId());
    }
    
    protected function newMultiEntity() {
        return new CompanytoNetwork();  // "prázdná" entita pro formulář pro přidání
    }

    protected function cardinality() {
        return FamilyInterface::CARDINALITY_0_N;
    }    

    private function getCheckedNetworks() {
        $company = $this->companyRepo->get($this->getFamilyRouteSegment()->getParentId());
        // načte vazební tabulku  - $companyToNetworks pro danou company
//        if ($this->isMultiEditable()) {
//            $companyToNetworks = $this->companyToNetworkRepo->findByCompanyId($company->getId());
//        } else {
            $companyToNetworks = $this->companyToNetworkRepo->find("company_id=:company_id AND published=1", ["company_id"=>$company->getId()]);
//        }        
        $checkedNetworks = [];
        foreach ($companyToNetworks as $companyToNetwork) {
            $checkedNetworks[] = $this->networkRepo->get($companyToNetwork->getNetworkId());
        }        
        return $checkedNetworks;
    }
    
    protected function loadMultiEntitiesMap() {
        // pro renderování tagů - pro editaci potřebujii všechny tagy, pro needitovatelnou verzi jen tagy jobu (checked)
        if (!$this->multiEntitiesMap) {
            if ($this->isMultiEditable()) {
                $networks = $this->networkRepo->findAll();
                $this->selectedEntities = $this->getCheckedNetworks();
            } else {
                $networks = $this->getCheckedNetworks();
            }
            $this->multiEntitiesMap = [];
            foreach ($networks as $network) {
                $this->multiEntitiesMap[$network->getId()] = $network;
            }
        }
    }
    
    private function checkboxTemplateData($items) {
        $allNetworks=[];
        // map jsou networky indexované podle id networků (se stejnou map byly renderovány items)
        $map = $this->multiEntitiesMap;
        /** @var NetworkInterface  $network */
        foreach ( $map as $id => $network) {
            $label = $items[$id];  //$items jsou remderované networky indexované podle id tagů
            $allNetworks[$label] = ["data[{$network->getIcon()}]" => $network->getId()] ;
        }        

        $checkedNetworks=[];   //networky pro 1 company
        foreach ($this->selectedEntities as $network) {
            /** @var NetworkInterface $network */
            $checkedNetworks["data[{$network->getIcon()}]"] = $network->getId();
        }
        return [
            'id' => $this->getFamilyRouteSegment()->getParentId(),
            'allCheckboxes'=>$allNetworks,
            'checkedCheckboxes'=>$checkedNetworks
        ];
    }
    
    /**
     * 
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator {
        if ($this->isMultiEditable()) {
            $componentRouteSegment = $this->getFamilyRouteSegment();
            $array = array_merge(
                [         
                'listHeadline'=>'Sledujte nás - sítě firmy',           
                //route
                'actionSave' => $componentRouteSegment->getAddPath(),    // pracuje jen s kolekcí -> nejsou routy s id jednotlivých job to tag
                ]
                , $this->checkboxTemplateData($this->getArrayCopy())          
            );        
        } else {
            $array = [
                'listHeadline'=>'Sledujte nás',           
            'items'=>$this->getArrayCopy()
            ];
        }        

        $this->appendData($array);
        return parent::getIterator();        
    }
}