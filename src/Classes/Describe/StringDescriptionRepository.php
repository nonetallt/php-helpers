<?php

namespace Nonetallt\Helpers\Describe;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use Jawira\CaseConverter\Convert;
use Nonetallt\Helpers\Strings\Str;

class StringDescriptionRepository
{
    use FindsReflectionClasses;

    private $mapping;

    public function __construct()
    {
        $refs = $this->findReflectionClasses(__DIR__, __NAMESPACE__, StringDescription::class);
        $this->mapping = [];

        foreach($refs as $ref) {
            $class = $ref->getShortName();
            $converter = new Convert($class);
            $alias = $converter->fromPascal()->toSnake();
            $alias = Str::before($alias, '_');
             
            $this->mapping[$alias] = $ref->name;
        }
    }

    public function getDescription($value, bool $pretty = false)
    {
        $type = strtolower(gettype($value));

        if($type === 'string') return $value;
        if($type === 'double') $type = 'float';

        if(! isset($this->mapping[$type])) {
            throw new \Exception("Unexpected type $type");
        }

        $desc = $this->mapping[$type];

        return $pretty ? $desc::prettyDescription($value, $this) : $desc::description($value, $this);
    }
}
