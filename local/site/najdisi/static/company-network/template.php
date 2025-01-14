<?php
use Pes\View\Renderer\PhpTemplateRendererInterface;
use Site\ConfigurationCache;

use Pes\Text\Text;
use Pes\Text\Html;

use Events\Model\Repository\CompanytoNetworkRepoInterface;
use Events\Model\Repository\CompanytoNetworkRepo;
use Events\Model\Repository\NetworkRepoInterface;
use Events\Model\Repository\NetworkRepo;

/** @var NetworkRepo $networkRepo */
$networkRepo= $container->get(NetworkRepo::class);

/** @var CompanytoNetworkRepo $companyToNetworkRepo */
$companyToNetworkRepo= $container->get(CompanytoNetworkRepo::class);

$networks = $networkRepo->findAll();
$compToNetworks = $companyToNetworkRepo->findAll();
foreach ($compToNetworks as $compToNet) {
    $companyNets = $companyToNetworkRepo->findByCompanyId($compToNet->getCompanyId());
    $netUsage = $companyToNetworkRepo->findByNetworkId($compToNet->getNetworkId());

}
