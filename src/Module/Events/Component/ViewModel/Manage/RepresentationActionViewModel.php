<?php
namespace Events\Component\ViewModel\Manage;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\RepresentativeRepoInterface;
use Events\Model\Repository\CompanyRepoInterface;

use Events\Component\ViewModel\Manage\RepresentationActionViewModelInterface;
use Events\Model\Entity\RepresentativeInterface;

use ArrayIterator;

/**
 * Description of RepresentativeActionViewModel
 *
 * @author pes2704
 */
class RepresentationActionViewModel extends ViewModelAbstract implements RepresentationActionViewModelInterface {

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
        $representativeActions = $this->status->getRepresentativeActions();
        $repsesentative = isset($representativeActions) ? $representativeActions->getRepresentative() : null;
        $placeholderValue = '';
        $array = [
            'loginName' => $this->status->getUserLoginName(),
            'idCompanyArray' => $this->createIdCompanyArray($this->status->getUserLoginName(), $placeholderValue),
            'selectedCompanyId' => isset($repsesentative) ? $repsesentative->getCompanyId() : null,
            'placeholderValue' => $placeholderValue
        ];
        return new ArrayIterator($array);
    }
    public function isMultiRepresentative(): bool {
        return (count($this->getRepresentativesByLoginName($loginName)))>1 ? true : false;
    }
    /**
     * Generuje pole company id=>name, první položku vytvoří práznou (''=>'') pro select element required
     * @param type $loginName
     * @return type
     */
    private function createIdCompanyArray($loginName, $placeholder) {
        $a = [];
        $a[$placeholder] = 'Vyberte zastupovanou firmu';  // klíč = hodnota první položky $idCompanyArray pro zajištění funkčnosti required select
        $representatives = $this->getRepresentativesByLoginName($loginName);
        foreach ($representatives as $representative) {
            /** @var RepresentativeInterface $representative */
            $a[$representative->getCompanyId()] = $this->companyRepo->get($representative->getCompanyId())->getName();
        }
        return $a;
    }
    
    private function getRepresentativesByLoginName($loginName): array {
        return $this->representativeRepo->findByLoginName($loginName);
    }
}
