<?php

namespace Test\Unit\Common;

use PHPUnit\Framework\TestCase;
use Test\Mock\SettingsMock;

class SettingsTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function testGetSettingValueReturnsSetValue()
    {
        $settings = new SettingsMock();
        $settings->setSettingValue('foo', 'bar');
        $this->assertEquals('bar', $settings->getSettingValue('foo'));
    }

    public function testHasSettingReturnsTrue()
    {
        $settings = new SettingsMock();
        $this->assertTrue($settings->hasSetting('foo'));
    }

    public function testHasSettingReturnsFalse()
    {
        $settings = new SettingsMock();
        $this->assertFalse($settings->hasSetting('foobar'));
    }

    public function testGetAllReturnsSetValues()
    {
        $settings = new SettingsMock();
        $set = ['foo' => 'test1', 'bar' => 'test2', 'baz' => 1];
        $settings->setAll($set);
        $this->assertEquals($set, $settings->getAll());
    }

    public function testValuesCanBeSetAndGetUsingProperties()
    {
        $settings = new SettingsMock();
        $settings->foo = 'bar';
        $this->assertEquals('bar', $settings->foo);
    }
}
