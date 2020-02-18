<?php

namespace Nonetallt\Helpers\Validation;

use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClassRepository;

class ValidationRuleRepository extends ReflectionClassRepository
{
    CONST COLLECTION_TYPE = ValidationRule::class;

    public function __construct()
    {
        parent::__construct();

        $dir = __DIR__ . '/Rules';
        $namespace = __NAMESPACE__ . '\\Rules';
        $this->loadReflections($dir, $namespace);
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
