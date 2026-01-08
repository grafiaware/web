<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyRepo;

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$name = $statusViewModel->getPresentedMenuItem()->getTitle();

/** @var CompanyRepoInterface $companyRepo */
$companyRepo = $container->get(CompanyRepo::class);
$company = $companyRepo->find(" name LIKE :name_like AND version_fk=:version_fk", [":name_like"=>"%$name%", "version_fk"=>'archive_2025']);
$cnt = count($company);
if ($cnt==0) {
    echo "Nenalezena company podle jména '$name'.";
} elseif($cnt>1) {
    echo "Nalezeno více company podle jména '$name'. Počet nalezených: $cnt";
} else {
    $companyId = $company[0]->getId();
}
if (!isset($companyId)) {
    echo "Není company id.";
}