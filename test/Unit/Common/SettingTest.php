<?php

namespace Test\Unit\Common;

use PHPUnit\Framework\TestCase;
use Nonetallt\Helpers\Common\Setting;
use Nonetallt\Helpers\Generic\MissingValue;
use Nonetallt\Helpers\Validation\Validators\ArrayValidator;
use Nonetallt\Helpers\Validation\Exceptions\ValidationException;

class SettingTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function testValueIsMissingByDefault()
    {
        $setting = new Setting('foo');
        $this->assertFalse($setting->hasValue());
    }

    public function testValueCanBeSet()
    {
        $setting = new Setting('foo');
        $setting->setValue('bar');
        $this->assertEquals('bar', $setting->getValue());
    }

    public function testDefaultValueIsEnabledByDefault()
    {
        $setting = new Setting('foo', null, 'bar');
        $this->assertEquals('bar', $setting->getValue());
    }

    public function testDefaultValueIsUsedIfEnabled()
    {
        $setting = new Setting('foo', null, 'bar');
        $this->assertEquals('bar', $setting->getValue(true));
    }

    public function testDefaultValueIsNotUsedIfDisabled()
    {
        $setting = new Setting('foo', null, 'bar');
        $this->assertInstanceOf(MissingValue::class, $setting->getValue(false));
    }

    public function testActualValueIsUsedInsteadOfDefault()
    {
        $setting = new Setting('foo', null, 'baz');
        $setting->setValue('bar');
        $this->assertEquals('bar', $setting->getValue());
    }

    public function testSettingCanFailValidation()
    {
        $this->expectException(ValidationException::class);
        $setting = new Setting('foo', ArrayValidator::fromArray(['validate' => 'integer']), 'bar');
        $setting->setValue('bar');
    }

    public function testCanPassValidation()
    {
        $setting = new Setting('foo', ArrayValidator::fromArray(['validate' => 'string']), 'bar');
        $setting->setValue('baz');
        $this->assertEquals('baz', $setting->getValue());
    }

    public function testSettingCanFailValidateOfArrayItems()
    {
        $this->expectException(ValidationException::class);
        $setting = new Setting('foo', ArrayValidator::fromArray(['validate_items' => 'integer']), 'bar');
        $setting->setValue(['foo', 1]);
    }

    public function testCanPassValidationOfArrayItems()
    {
        $setting = new Setting('foo', ArrayValidator::fromArray(['validate_items' => 'string']), 'bar');
        $setting->setValue(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $setting->getValue());
    }

    public function testCanBeConstructedFromArray()
    {
        $setting = Setting::fromArray([
            'name' => 'test',
            'default' => 'foo',
            'validator' => ['validate' => 'string']
        ]);


        $setting->setValue('bar');
        $this->assertEquals('foo', $setting->getDefaultValue());
    }
}
