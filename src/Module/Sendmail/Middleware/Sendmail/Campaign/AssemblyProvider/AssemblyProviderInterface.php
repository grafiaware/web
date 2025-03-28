<?php
namespace Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider;

use Mail\AssemblyInterface;

/**
 *
 * @author vlse2610
 */
interface AssemblyProviderInterface {
    
    const ASSEMBLY_ANKETA_2025 = "sestava pro maily návštěvníkům, kteří vyplnili anketu 2025";
    const ASSEMBLY_DVA = "druhá sestava";
    
    /**
     * 
     * @param string $assemblyName
     * @param string $mailAdresata
     * @param string $jmenoAdresata
     * @return AssemblyInterface
     */
    public function getAssembly($assemblyName, $mailAdresata, $jmenoAdresata): AssemblyInterface;
     
     
}



