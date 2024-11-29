<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelChildListInterface;
use Pes\Type\ContextData;

/**
 * Description of ViewModelChildListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelChildListAbstract extends ContextData implements ViewModelChildListInterface {
    
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
