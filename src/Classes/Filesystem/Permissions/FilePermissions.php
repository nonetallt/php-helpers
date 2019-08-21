<?php

namespace Nonetallt\Helpers\Filesystem\Permissions;

use Nonetallt\Helpers\Filesystem\Exceptions\PermissionException;

class FilePermissions
{
    /**
     * fopen() mode permission requirements
     */
    CONST MODE_REQUIREMENTS = [
        'r'  => ['read' => true, 'write' => false],
        'r+' => ['read' => true, 'write' => true],
        'w'  => ['read' => false, 'write' => true],
        'w+' => ['read' => true, 'write' => true],
        'a'  => ['read' => false, 'write' => true],
        'a+' => ['read' => true, 'write' => true],
        'x'  => ['read' => false, 'write' => true],
        'x+' => ['read' => true, 'write' => true],
        'c'  => ['read' => false, 'write' => true],
        'c+' => ['read' => true, 'write' => true],
        'e' => []
    ];

    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Exceptions\PermissionException
     */
    public function validateStreamMode(string $mode)
    {
        $requirements = self::MODE_REQUIREMENTS[$mode] ?? null;

        if($requirements === null) {
            "mode '$mode' does not exist";
            throw new \Exception($msg);
        }

        $missingPermissions = [];

        foreach($requirements as $permission => $required) {
            /* Skip if permission is not required */
            if(! $required) continue;

            /* Skip if permission is fullfilled */
            if($this->hasPermission($permission)) continue;

            /* Record missing permissions */
            $missingPermissions[] = $permission;
            
        }

        if(empty($missingPermissions)) return;

        $missing = implode(', ', $missingPermissions);
        $msg = "Missing required permissions [$missing] for stream mode '$mode'";
        throw new PermissionException($msg, $this->path);
    }

    public function isStreamModeValid(string $mode) : bool
    {
        try {
            $this->validateMode($mode);
        }
        catch(PermissionException $e) {
            return false;
        }

        return true;
    }

    public function hasPermission(string $operation)
    {
        if($operation === 'read') return $this->isReadable();
        if($operation === 'write') return $this->isWritable();

        $msg = "Operation '$operation' is not valid";
        throw new \Exception($msg);
    }

    public function isReadable() : bool
    {
        return is_readable($this->path);
    }

    public function isWritable() : bool
    {
        $writable_file = (file_exists($this->path) && is_writable($this->path));
        $writable_directory = (!file_exists($this->path) && is_writable(dirname($this->path)));

        if ($writable_file || $writable_directory) {
            return true;

        }
        return false;
    }
}
