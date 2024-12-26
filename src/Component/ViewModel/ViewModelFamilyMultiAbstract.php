<?php
namespace Component\ViewModel;

use Component\ViewModel\FamilyInterface;
use Component\ViewModel\ViewModelMultiAbstract;
use Component\ViewModel\FamilyTrait;

/**
 * Description of ViewModelChildListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelFamilyMultiAbstract extends ViewModelMultiAbstract implements FamilyInterface {
    
    use FamilyTrait;


}
