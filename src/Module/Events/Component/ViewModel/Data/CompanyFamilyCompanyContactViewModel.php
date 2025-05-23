<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyContactRepoInterface;
use Events\Model\Entity\CompanyContactInterface;
use Model\Entity\EntityInterface;


use Exception;
use TypeError;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyCompanyContactViewModel extends ViewModelFamilyItemAbstract {
    
    private $status;
    private $companyRepo;
    private $companyContactRepo;
    
    /**
     * 
     * @var CompanyContactInterface
     */
    private $companyContact;


    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyContactRepoInterface $companyContactRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyContactRepo = $companyContactRepo;
    }

    use RepresentativeTrait;    
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof CompanyContactInterface) {
            $this->companyContact = $entity;
            $this->getFamilyRouteSegment()->setChildId($this->companyContact->getId());
        } else {
            $cls = CompanyContactInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isItemEditable(): bool {
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
    
    private function loadCompanyContact() {
        if (!isset($this->companyContact)) {
            if ($this->getFamilyRouteSegment()->hasChildId()) {
                $this->companyContact = $this->companyContactRepo->get($this->getFamilyRouteSegment()->getChildId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadCompanyContact();
        $componentRouteSegment = $this->getFamilyRouteSegment();        
        if ($componentRouteSegment->hasChildId()) {        
            $companyContactArray = [
                //route
                'actionSave' => $componentRouteSegment->getSavePath(),
                'actionRemove' => $componentRouteSegment->getRemovePath(),
                // data
                'fields' => [
//                    'editable' => $editableItem,
                    'name' =>  $this->companyContact->getName(),
                    'phones' =>  $this->companyContact->getPhones(),
                    'mobiles' =>  $this->companyContact->getMobiles(),
                    'emails' =>  $this->companyContact->getEmails(),
                    ],                      
                ];
        } elseif ($this->isItemEditable()) {
            $companyContactArray = [
                // text
                'addHeadline' => 'Přidej kontakt',                 
                //route
                'actionAdd' => $componentRouteSegment->getAddPath(),
                // data
                'fields' => [
//                    'editable' => $editableItem,
                    ]
            ];
        } else {
            $companyContactArray = [];
        }
        
        
        $this->appendData($companyContactArray);
        return parent::getIterator();        
    }
}
