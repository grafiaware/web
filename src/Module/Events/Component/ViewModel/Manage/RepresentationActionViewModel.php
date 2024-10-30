<?php
namespace Events\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Model\Entity\RepresentativeInterface;

use Events\Middleware\Events\Controler\RepresentationControler;
use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class RepresentationActionViewModel extends ViewModelAbstract {

    private $status;
    
    private $representativeRepo;
    
    private $companyRepo;

    public function __construct(
            StatusViewModelInterface $status,
            RepresentativeRepoInterface $representativeRepo,
            CompanyRepoInterface $companyRepo
            ) {
        $this->status = $status;
        $this->representativeRepo = $representativeRepo;
        $this->companyRepo = $companyRepo;
    }

    public function getIterator() {
        $representative = $this->status->getRepresentativeActions()->getRepresentative();
        $placeholderValue = '';
        $array = [
            'loginName' => $this->status->getUserLoginName(),
            'idCompanyArray' => $this->createIdCompanyArray($this->status->getUserLoginName(), $placeholderValue),
            'selectedCompanyId' => isset($representative) ? $representative->getCompanyId() : null,
            'placeholderValue' => $placeholderValue
        ];
        return new ArrayIterator($array);
    }

    /**
     * Generuje pole company id=>name, první položku vytvoří práznou (''=>'') pro select element required
     * @param type $loginName
     * @return type
     */
    private function createIdCompanyArray($loginName, $placeholder) {
        $a = [];
        $a[$placeholder] = 'Vyberte zastupovanou firmu';  // klíč = hodnota první položky $idCompanyArray pro zajištění funkčnosti required select
        $representatives = $this->representativeRepo->findByLoginName($loginName);
        foreach ($representatives as $representative) {
            /** @var RepresentativeInterface $representative */
            $a[$representative->getCompanyId()] = $this->companyRepo->get($representative->getCompanyId())->getName();
        }
        return $a;
    }
}
