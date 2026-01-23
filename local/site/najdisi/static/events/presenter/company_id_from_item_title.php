<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
/** @var PhpTemplateRendererInterface $this */

use Component\ViewModel\StatusViewModel;
use Component\ViewModel\StatusViewModelInterface;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\CompanyRepo;

/**
 * Vytvoří proměnnou $companyId pro statickou stránku presenter.
 * 
 * Použije titulek prezetovaného menu item jako jméno firmy a z databáze přečte id firmy s tímto jménem.
 * Stránka prezenter vznikne tak, že editor vytvoří statickou stránku (events static) a nastaví template "presenter" nebo "archive presenter" a případné další. 
 * Titulek takové stránky pak nastaví na název firmy.
 * 
 * Titulek nemusí odpovídat vždy zcela přesně názvu firmy - stačí když obsahuje část názvu firmy, to znamený, že celý text titulku musí být obsažen v názvu firmy (přesně).
 * Hodá se to pro situace, kdy v titulku nemusí být např. právní forma (", a.s." nebo "státní podnik" apod.).
 */


/** @var StatusViewModelInterface $statusViewModel */
$statusViewModel = $container->get(StatusViewModel::class);
$itemTitle = $statusViewModel->getPresentedMenuItem()->getTitle();

/** @var CompanyRepoInterface $companyRepo */
$companyRepo = $container->get(CompanyRepo::class);
$company = $companyRepo->find(" name LIKE :name_like AND version_fk=:version_fk", [":name_like"=>"%$itemTitle%", "version_fk"=>$version_fk]);
$cnt = count($company);
if ($cnt==0) {
    echo "<p>Chyba: Nenalezena company podle textu položky menu '$itemTitle' s verzí '$version_fk'.</p>";
} elseif($cnt>1) {
    echo "<p>Chyba: Nalezeno více company podle textu položky menu '$itemTitle' s verzí '$version_fk'. Počet nalezených: $cnt.</p>";
} else {
    $companyId = $company[0]->getId();
}
if (!isset($companyId)) {
    echo "<p>Chyba: Není company id.</p>";
}