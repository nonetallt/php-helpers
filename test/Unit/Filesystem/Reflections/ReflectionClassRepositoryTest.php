<?php

namespace Test\Unit\Filesystem\Reflections;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\Reflections\ReflectionClassRepository;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;

class ReflectionClassRepositoryTest extends TestCase
{
    public function testCallerDirAndNamespaceCanBeUsedAsDefaultValuesToFindTheCallerClass()
    {
        $repo = new ReflectionClassRepository();
        $repo->loadReflections(__DIR__, __NAMESPACE__, TestCase::class);
        $tests = $repo->map(function($ref) {
            return $ref->name;
        });

        /* Assert that this class is in the default reflection */
        $this->assertTrue(in_array(self::class, $tests));
    }

    public function testNonExistentPathCantBeSetAsDir()
    {
        $this->expectException(FileNotFoundException::class);
        $repo = new ReflectionClassRepository();
        $repo->loadReflections('foobar', null, TestCase::class);
    }

    public function testFileCannotBeSeAsDir()
    {
        $this->expectException(TargetNotDirectoryException::class);
        $repo = new ReflectionClassRepository();
        $repo->loadReflections(__FILE__, null, TestCase::class);
    }
}
