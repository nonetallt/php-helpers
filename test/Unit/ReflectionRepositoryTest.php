<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Filesystem\ReflectionRepository;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;

class ReflectionRepositoryTest extends TestCase
{
    public function testCallerDirAndNamespaceCanBeUsedAsDefaultValuesToFindTheCallerClass()
    {
        $repo = new ReflectionRepository(TestCase::class, __DIR__, __NAMESPACE__);
        $tests = $repo->map(function($ref) {
            return $ref->name;
        });

        /* Assert that this class is in the default reflection */
        $this->assertTrue(in_array(self::class, $tests));
    }

    public function testNonExistentPathCantBeSetAsDir()
    {
        $this->expectException(FileNotFoundException::class);
        $repo = new ReflectionRepository(TestCase::class, 'foobar');
    }

    public function testFileCannotBeSeAsDir()
    {
        $this->expectException(TargetNotDirectoryException::class);
        $repo = new ReflectionRepository(TestCase::class, __FILE__);
    }
}
