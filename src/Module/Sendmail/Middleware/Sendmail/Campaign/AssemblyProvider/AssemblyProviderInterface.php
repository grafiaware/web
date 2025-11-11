<?php
namespace Sendmail\Middleware\Sendmail\Campaign\AssemblyProvider;

use Mail\AssemblyInterface;

/**
 *
 * @author vlse2610
 */
interface AssemblyProviderInterface {
    // na hodnotě konstanta "nezáleží" - skript bude funkční s libovolnou hodnotou
    // hodnota konstanty se zapíše do csv souboru do sloupce campaing assembly
    const ASSEMBLY_ANKETA_2025 = "maily VPV anketa 2025";
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



