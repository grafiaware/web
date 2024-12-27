<?php
namespace Component\ViewModel;

use Component\ViewModel\FamilyInterface;
use Component\ViewModel\ViewModelListAbstract;
use Component\ViewModel\FamilyTrait;

/**
 * Description of ViewModelChildListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelFamilyListAbstract extends ViewModelListAbstract implements FamilyInterface {
    
    use FamilyTrait;


}
