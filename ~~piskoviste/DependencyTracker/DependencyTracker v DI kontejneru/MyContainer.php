<?php
use Psr\Container\ContainerInterface;

class MyContainer implements ContainerInterface {
    private $instances = [];
    private $factories = [];
    private $trackedInstances = [];

    public function set(string $id, callable $factory) {
        $this->factories[$id] = $factory;
    }

    public function get(string $id) {
        if (!isset($this->instances[$id])) {
            if (!isset($this->factories[$id])) {
                throw new Exception("Service {$id} not found.");
            }

            $this->instances[$id] = $this->factories[$id]($this);
            
            // Registrace sledovaných instancí
            $this->trackInstance($this->instances[$id]);
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool {
        return isset($this->factories[$id]) || isset($this->instances[$id]);
    }

    private function trackInstance(object $instance) {
        $this->trackedInstances[spl_object_id($instance)] = $instance;
    }

    public function executeAll(string $methodName) {
        foreach ($this->trackedInstances as $instance) {
            if (method_exists($instance, $methodName)) {
                $instance->$methodName();
            }
        }
    }
}
