<?php
use Pes\Text\Text;
use Pes\Text\Html;
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */
use Component\ViewModel\StatusViewModelInterface;
use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyRepo;

/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
/** @var CompanyRepoInterface $companyRepo */
$companyRepo = $container->get(CompanyRepo::class);
$company = $companyRepo->find("LIKE %:name%", [":name"=>$name]);
$cnt = count($company);
if ($cnt==0) {
    echo "Nenalezena company podle jména '$name'.";
} elseif($cnt>1) {
    echo "Nenalezeno více company podle jména '$name'. Počet nalezených: $cnt";
} else {
    $companyId = $company[0]->getId();
}
if (!isset($companyId)) {
    echo "Není company id.";
}