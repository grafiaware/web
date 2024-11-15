<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Events\Middleware\Events\Controler;
use FrontControler\FrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\RequestParams;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;

use Events\Model\Repository\RepresentativeRepoInterface;

use Status\Model\Enum\FlashSeverityEnum;

/**
 * Description of RepesenatationControler
 *
 * @author pes2704
 */
class RepresentationControler extends FrontControlerAbstract {
    
    const FORM_REPRESENTATION_COMPANY_ID = "form_representation_company_id";
    const FORM_REPRESENTATION_EDIT_DATA = "form_representation_edit_data";
    private $representativeRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusFlashRepo $statusFlashRepo,
            StatusPresentationRepo $statusPresentationRepo,
            RepresentativeRepoInterface $representativeRepo

        ) {
        parent::__construct($statusSecurityRepo, $statusFlashRepo, $statusPresentationRepo);
        $this->representativeRepo = $representativeRepo;
    }
    public function setRepresentation(ServerRequestInterface $request) {
        $loginLoginName = $this->statusSecurityRepo->get()->getLoginAggregate()->getLoginName();
        $companyId = (new RequestParams())->getParsedBodyParam($request, self::FORM_REPRESENTATION_COMPANY_ID);
        $editData = (new RequestParams())->getParsedBodyParam($request, self::FORM_REPRESENTATION_EDIT_DATA);
        
        if (isset($loginLoginName) AND isset($companyId)) {
            $representative = $this->representativeRepo->get($loginLoginName, $companyId);
        }
        if (isset($representative)) {
            // nastavení aktuálního repesentative ve statusu
            $repreActions = $this->statusSecurityRepo->get()->getRepresentativeActions();
            $repreActions->setRepresentative($representative);
            $repreActions->setDataEditable($editData);
            $this->addFlashMessage("set repsentative company '$companyId'", FlashSeverityEnum::INFO);
        } else {
            $this->addFlashMessage("unable to set representation of company '$companyId'", FlashSeverityEnum::WARNING);            
        }

        return $this->redirectSeeLastGet($request); // 303 See Other
    }
}
