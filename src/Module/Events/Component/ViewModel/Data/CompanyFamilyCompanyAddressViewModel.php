<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelFamilyItemAbstract;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyAddressRepoInterface;
use Events\Model\Entity\CompanyAddressInterface;
use Model\Entity\EntityInterface;

use Access\Enum\RoleEnum;
use TypeError;
use Exception;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyFamilyCompanyAddressViewModel extends ViewModelFamilyItemAbstract {
    
    private $status;
    private $companyRepo;
    private $companyAddressRepo;
    /**
     * 
     * @var CompanyAddressInterface
     */
    private $companyAddress;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo,
            CompanyAddressRepoInterface $addressRepo            
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
        $this->companyAddressRepo = $addressRepo;
    }

    use RepresentativeTrait;
    
    public function receiveEntity(EntityInterface $entity) {
        if ($entity instanceof CompanyAddressInterface) {
            $this->companyAddress = $entity;
            $this->getFamilyRouteSegment()->setChildId($this->companyAddress->getCompanyId());  //pk = fk            
        } else {
            $cls = CompanyAddressInterface::class;
            $parCls = get_class($entity);
            throw new TypeError("Typ entity musí být $cls, předáno $parCls.");
        }
    }
    
    public function isDataEditable(): bool {
        return $this->isCompanyEditor($this->getFamilyRouteSegment()->getParentId());
    }
    
    private function loadCompanyAddress() {
        if (!isset($this->companyAddress)) {
            if ($this->getFamilyRouteSegment()->hasChildId()) {
                $this->companyAddress = $this->companyAddressRepo->get($this->getFamilyRouteSegment()->getChildId());     
            } else {
                throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
            }
        }
    }
    
    public function getIterator() {
        $this->loadCompanyAddress();
        $componentRouteSegment = $this->getFamilyRouteSegment();
        if ($componentRouteSegment->hasChildId()) {        
            $companyAddrArray = [
                //route
                    'actionSave' => $componentRouteSegment->getSavePath(),
                    'actionRemove' => $componentRouteSegment->getRemovePath(),
                // data
                'fields' => [
                    'name'   => $this->companyAddress->getName(),
                    'lokace' => $this->companyAddress->getLokace(),
                    'psc'    => $this->companyAddress->getPsc(),
                    'obec'   => $this->companyAddress->getObec()
                ],                
            ];
        } else {
            /** @var CompanyInterface $company */ 
            if ($this->companyRepo->get($this->getFamilyRouteSegment()->getParentId())) {  // validace id rodiče
                if ($this->isDataEditable()) {
                    $companyAddrArray = [
                        // text
                        'addHeadline' => 'Přidej adresu',                      
                        //route
                        'actionAdd' => $componentRouteSegment->getAddPath(),
                        // data
                        'fields' => []                    
                        ];        
                } else {
                    $companyAddrArray = [];
                }
            }
        }
        $this->appendData($companyAddrArray);
        return parent::getIterator();        
    }
}
