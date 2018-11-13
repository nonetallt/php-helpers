<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase
{

    public function testRequiredInArray()
    {
        required_in_array('Kappa', ['Keepo', 'Koopa', 'Kappa']);
        $this->assertTrue(true);
    }

    public function testRequiredInArrayFails()
    {
        $this->expectExceptionMessage('Required value Kapa not found in array [Keepo, Koopa, Kappa]');
        required_in_array('Kapa', ['Keepo', 'Koopa', 'Kappa']);
    }

    public function testInArrayRequired()
    {
        in_array_required('Kappa', ['Keepo', 'Koopa', 'Kappa']);
        $this->assertTrue(true);
    }

    public function testInArrayFails()
    {
        $this->expectExceptionMessage('Value Kapa must be one of the values in array [Keepo, Koopa, Kappa]');
        in_array_required('Kapa', ['Keepo', 'Koopa', 'Kappa']);
    }

    public function testArrayKeysExist()
    {
        $keys = ['Kappa', 'Keepo'];
        $array = ['test' => 1, 'Keepo' => 2, 'Kappa' => 3];
        $this->assertTrue(array_keys_exist($keys, $array));
    }

    public function testArrayKeysDontExist()
    {
        $keys = ['Kappa', 'Keepo'];
        $array = ['test' => 1, 'Keeo' => 2, 'appa' => 3];
        $this->assertFalse(array_keys_exist($keys, $array));
    }

    public function testArrayKeysMissing()
    {
        $keys = ['Kappa', 'Keepo'];
        $array = ['test' => 1, 'Keepo' => 2, 'appa' => 3];

        $this->assertEquals(['Kappa'], array_keys_missing($keys, $array));
    }

    public function testArrayKeysNotMissing()
    {
        $keys = ['Kappa', 'Keepo'];
        $array = ['test' => 1, 'Keepo' => 2, 'Kappa' => 3];

        $this->assertEmpty(array_keys_missing($keys, $array));
    }
}
