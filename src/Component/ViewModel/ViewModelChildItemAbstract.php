<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelChildInterface;
use Component\ViewModel\ViewModelAbstract;

/**
 * Description of ViewModelChildListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelChildItemAbstract extends ViewModelAbstract implements ViewModelChildInterface {
    
    private $id;

    public function setParentId(string $id) {
        $this->id = $id;
    }
    
    public function hasParentId(): bool {
        return isset($this->id);
    }
    
    public function getParentId(): string {
        return $this->id ?? null;
    }
}
