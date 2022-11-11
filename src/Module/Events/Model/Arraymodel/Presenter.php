<?php
namespace Events\Model\Arraymodel;

use Events\Model\Repository\CompanyRepoInterface;
use Events\Model\Repository\JobRepoInterface;


use Events\Model\Repository\VisitorJobRequestRepo;
use Events\Model\Repository\VisitorProfileRepoRepo;



/**
 * Description of Presenter
 *
 * @author pes"daikin"7"na""mdelektronik"
 */
class Presenter {
    private $company = [
        "na" => ["name" => "Nepožádali o zastupování zaměstnavatele", "eventInstitutionName"=>"na", "shortName" => "na"],
        "wienerberger" => ["name" => "Wienerberger s.r.o.", "eventInstitutionName"=>"'Wienerberger'", "shortName" => "wienerberger"],
        "daikin" => ["name" =>"Daikin Industries Czech Republic s.r.o.", "eventInstitutionName"=>"'Daikin'", "shortName" => "daikin"],
        "akka" => ["name" => "AKKA Czech Republic s.r.o.", "eventInstitutionName"=>"AKKA Czech Republic", "shortName" => "akka"],
        "mdelektronik" => ["name" => "MD ELEKTRONIK spol. s r.o.", "eventInstitutionName"=>"MD Elektronik", "shortName" => "mdelektronik"],
        "konplan" => ["name" => "Konplan s.r.o.", "eventInstitutionName"=>"Konplan", "shortName" => "konplan"],
        "valeo" => ["name" => "Valeo Autoklimatizace k.s.", "eventInstitutionName"=>"Valeo Autoklimatizace", "shortName" => "valeo"],
        "serw" => ["name" => "SERW, spol. s r.o.", "eventInstitutionName"=>"Serw", "shortName" => "serw"],
        "stoelzle" => ["name" => "Stoelzle Union s.r.o.", "eventInstitutionName"=>"Stoelzle", "shortName" => "stoelzle"],
        "grafia" => ["name" => "Grafia s.r.o.", "eventInstitutionName"=>"Grafia", "shortName" => "grafia"],
        "dzk" => ["name" => "Drůbežářské závody Klatovy a.s.", "eventInstitutionName"=>"DZ Klatovy", "shortName" => "dzk"],
        "kermi" => ["name" => "Kermi s. r. o.", "eventInstitutionName"=>"Kermi", "shortName" => "kermi"],
        "possehl" => ["name" => "Possehl Electronics Czech Republic s.r.o. ", "eventInstitutionName"=>"Possehl", "shortName" => "possehl"],

        ];

        // musí mít nastavenu roli "presenter" v credentials
        private $person =
            [
            'Krejčová Barbora' => ['regname' => "Krejčová Barbora", 'regmail' => "barbora.krejcova@wienerberger.com", 'regcompany' => "Wienerberger s.r.o.", 'shortName'=>"wienerberger"],

            'Tomáš Matoušek' => ['regname' => "Tomáš Matoušek", 'regmail' => "matousek.t@daikinczech.cz", 'regcompany' => "Daikin Industries Czech Republic s.r.o.", 'shortName'=>"daikin"],

            'Elizabeth Franková' => ['regname' => "Elizabeth Franková", 'regmail' => "Elizabeth.frankova@akka.eu", 'regcompany' => "Akka Czech Republice s.r.o.", 'shortName'=>"akka"],
            'KaterinaJanku' => ['regname' => "KaterinaJanku", 'regmail' => "katerina.janku@akka.eu", 'regcompany' => "AKKA Czech Republic", 'shortName'=>"akka"],
            'Šárka Bilíková' => ['regname' => "Šárka Bilíková", 'regmail' => "sarka.bilikova@akka.eu", 'regcompany' => "AKKA Czech Republic s.r.o.", 'shortName'=>"akka"],
            'Vanda Štěrbová' => ['regname' => "Vanda Štěrbová", 'regmail' => "vanda.sterbova@akka.eu", 'regcompany' => "AKKA Czech Republic s.r.o.", 'shortName'=>"akka"],

            'VERONIKA' => ['regname' => "VERONIKA", 'regmail' => "Veronika.Simbartlova@md-elektronik.cz", 'regcompany' => "MD ELEKTRONIK s.r.o.", 'shortName'=>"mdelektronik"],
            'Zdeňka Obertíková' => ['regname' => "Zdeňka Obertíková", 'regmail' => "Zdenka.Obertikova@md-elektronik.cz", 'regcompany' => "MD ELEKTRONIK s.r.o.", 'shortName'=>"mdelektronik"],
            'Kristýna Křížová' => ['regname' => "Kristýna Křížová", 'regmail' => "kristyna.krizova@md-elektronik.cz", 'regcompany' => "MD ELEKTRONIK spol. s r.o.", 'shortName'=>"mdelektronik"],

            'Michaela Šebová' => ['regname' => "Michaela Šebová", 'regmail' => "michaela.sebova@stoelzle.com", 'regcompany' => "Stoelzle Union s.r.o.", 'shortName'=>"stoelzle"],  // nemá žádný event v EventListu

            'Jana Brabcová' => ['regname' => "Jana Brabcová", 'regmail' => "brabcova@grafia.cz", 'regcompany' => "Grafia s.r.o.", 'shortName'=>"grafia"],
            'Jana Brabcová Grafia OK' => ['regname' => "Jana Brabcová Grafia OK", 'regmail' => "brabcova@grafia.cz", 'regcompany' => "Grafia s.r.o.", 'shortName'=>"grafia"],
            'presenter' => ['regname' => "presenter", 'regmail' => "svoboda@grafia.cz", 'regcompany' => "TEST", 'shortName'=>"akka"],
            'katka2' => ['regname' => "katka2", 'regmail' => "85kacka58@seznam.cz", 'regcompany' => "Akka Czech Republice s.r.o.", 'shortName'=>"akka"],
            'katka3' => ['regname' => "katka3", 'regmail' => "85kacka58@seznam.cz", 'regcompany' => "Grafia s.r.o.", 'shortName'=>"grafia"],
            'Vlse Vystavovatel' => ['regname' => "Vlse Vystavovatel", 'regmail' => "selnerova@grafia.cz", 'regcompany' => "AKKA Czech Republic", 'shortName'=>"akka"],

#################
            'Romana' => ['regname' => "Romana", 'regmail' => "romana.nova@md-elektronik.cz", 'regcompany' => "MD ELEKTRONIK s.r.o.", 'shortName'=>"na"],
            'Jiří Čížek' => ['regname' => "Jiří Čížek", 'regmail' => "jiri.cizek@konplan.cz", 'regcompany' => "Konplan s.r.o.", 'shortName'=>"na"],
            'Lucie Veverková' => ['regname' => "Lucie Veverková", 'regmail' => "lucie.veverkova@konplan.cz", 'regcompany' => "Konplan s.r.o.", 'shortName'=>"na"],
            'Vendula Linková' => ['regname' => "Vendula Linková", 'regmail' => "vendula.linkova@konplan.cz", 'regcompany' => "Konplan s.r.o.", 'shortName'=>"na"],
            'Veronika Jíchová' => ['regname' => "Veronika Jíchová", 'regmail' => "veronika.jichova@konplan.cz", 'regcompany' => "Konplan s.r.o.", 'shortName'=>"na"],
                // a další


            ];

        
    
    /**
     * 
     * @var CompanyRepoInterface
     */
    private $companyRepo;    
    /**
     * 
     * @var JobRepoInterface
     */
    private $jobRepo;
    
    public function __construct( CompanyRepoInterface $companyRepo
                                 /*JobRepoInterface $jobRepo*/ ) {
                $this->companyRepo = $companyRepo;     
                //$this->jobRepo = $jobRepo;     
    }

            
            
            
            
            
            
//------------------------------------------------
    public function getPerson($loginName) {
        if (array_key_exists($loginName, $this->person)) {
            // join
            $person = $this->person[$loginName];
            return array_merge($person, $this->company[$person['shortName']]);
        }
    }

    public function getCompany($shortName) {
        return array_key_exists($shortName, $this->company) ? $this->company[$shortName] : [];
    }    
    
    public function getCompanyList() {
        return $this->company;
    }
    
    
    
    
    public function getCompanyListI() {
        $allCompanyObjects = $this->companyRepo->findAll();
        
        foreach  ($allCompanyObjects as $company) {
            $allCompanyArr [$company->getName()] =  ['id' => $company->getId(), 
                                                     'name' => $company->getName(), 
                                                     'eventInstitutionName30' => $company->getEventInstitutionName30() ];
        }

        
        //return  $allCompanyArr;
        return $allCompanyObjects;
    }

}
