<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClassRepository;

class ValidationRuleRepository extends ReflectionClassRepository
{
    private static $instance;

    public static function getInstance()
    {
        if(static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    private function __construct()
    {
        parent::__construct();

        $dir = __DIR__ . '/Rules';
        $namespace = __NAMESPACE__ . '\\Rules';
        $this->loadReflections(ValidationRule::class, $dir, $namespace);
    }

    /**
     * $override
     *
     */
    protected function filterClass(\ReflectionClass $ref) : bool
    {
        return ! $ref->isAbstract();
    }

    /**
     * @override
     *
     */
    protected function resolveAlias(\ReflectionClass $ref) : string
    {
        return ValidationRule::resolveName($ref);
    }
}
