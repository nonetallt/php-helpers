<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;

class ArraysTEst extends TestCase
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
}
