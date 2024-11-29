<?php
namespace Events\Component\ViewModel\Data;

use Component\ViewModel\ViewModelItemAbstract;
use Component\ViewModel\ViewModelItemInterface;

use Component\ViewModel\StatusViewModelInterface;
use Events\Component\ViewModel\Data\RepresentativeTrait;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Entity\CompanyInterface;

use Access\Enum\RoleEnum;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class CompanyViewModel extends ViewModelItemAbstract implements ViewModelItemInterface {

    private $status;  
    
    private $companyRepo;
    
    /**
     * 
     * @var CompanyInterface
     */
    private $company;
    
    public function __construct(
            StatusViewModelInterface $status,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->companyRepo = $companyRepo;
    }
    
    use RepresentativeTrait;
    
    private function isAdministrator() {
        return ($this->status->getUserRole()== RoleEnum::EVENTS_ADMINISTRATOR);
    }

    private function isCompanyRepresentative($companyId) {
        return ($this->getStatusRepresentativeDataEditable() AND $this->getStatusRepresentativeCompanyId()==$companyId);
    }
    
    public function getIterator() {     
        if ($this->hasItemId()) {
            $company = $this->companyRepo->get($this->getItemId());     
        } else {
            throw new Exception;// exception s kódem, exception musí být odchycena v kontroleru a musí způsobit jiný response ? 204 No Content
        }

        $editableItem = $this->isAdministrator() || $this->isCompanyRepresentative($company->getId());
        /** @var CompanyInterface $company */
        $item = [
            // conditions
            'editable' => $editableItem,    // vstupní pole formuláře jsou editovatelná
            // text
            'headline' => 'Název firmy',
            //route
            'componentRouteSegment' => 'events/v1/company',
            'id' => $company->getId(),
            // data
            'name' =>  $company->getName()                    
            ];
        return new ArrayIterator($item);
    }
}
