<?php

namespace Nonetallt\Helpers\Describe;

use Nonetallt\Helpers\Filesystem\Traits\FindsReflectionClasses;
use CaseConverter\CaseConverter;

class StringDescriptionRepository
{
    use FindsReflectionClasses;

    private $mapping;
    private $pretty;

    public function __construct()
    {
        $refs = $this->findReflectionClasses(__NAMESPACE__, __DIR__, StringDescription::class);
        $this->mapping = [];
        $this->pretty = false;

        foreach($refs as $ref) {
            $class = $ref->getShortName();
            $converter = new CaseConverter();
            $alias = $converter->convert($class)->from('studly')->to('snake');
            $alias = str_before($alias, '_');
             
            $this->mapping[$alias] = $ref->name;
        }
    }

    public function setPretty(bool $pretty)
    {
        $this->pretty = $pretty;
    }

    public function getDescription($value)
    {
        $type = strtolower(gettype($value));

        if($type === 'string') return $value;
        if($type === 'double') $type = 'float';

        if(! isset($this->mapping[$type])) {
            throw new \Exception("Unexpected type $type");
        }

        $desc = $this->mapping[$type];

        return $this->pretty ? $desc::prettyDescription($value, $this) : $desc::description($value, $this);
    }
}
