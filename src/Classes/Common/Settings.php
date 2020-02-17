<?php

namespace Nonetallt\Helpers\Common;

/**
 * Simple container for keyed Settings class instances
 *
 */
abstract class Settings
{
    private $items;

    public function __construct(array $values = [])
    {
        $this->items = [];
        foreach(static::defineSettings() as $settingData) {
            $setting = static::createSetting($settingData);
            $this->items[$setting->getName()] = $setting;
        }

        $this->setAll($values);
    }

    /**
     * Set all setting values
     *
     */
    public function setAll(array $settings)
    {
        /* Set user supplied values */
        foreach($settings as $key => $value) {
            $this->setSettingValue($key, $value);
        }
    }

    public function getAll(bool $useDefault = true) : array
    {
        return array_map(function($setting) use($useDefault) {
            return $setting->getValue($useDefault);
        }, $this->items);
    }

    public function hasSetting(string $name) : bool
    {
        return isset($this->items[$name]);
    }

    public function getSetting(string $name) : Setting
    {
        $setting = $this->items[$name] ?? null;

        if($setting !== null) {
            return $this->items[$name];
        }        

        $msg = "Setting '$name' does not exist";
        throw new SettingNotFoundException($msg);
    }

    public function getSettingValue(string $name, bool $useDefault = true)
    {
        return $this->getSetting($name)->getValue($useDefault);
    }

    public function setSettingValue(string $name, $value)
    {
        return $this->getSetting($name)->setValue($value);
    }

    public function __get(string $name)
    {
        return $this->getSettingValue($name);
    }

    public function __set(string $name, $value)
    {
        $this->setSettingValue($name, $value);
    }

    public function toArray(bool $filterMissing = false)
    {
        $result = [];
        foreach($this->items as $setting) {
            if($filterMissing && ! $setting->hasUsableValue()) continue;
            $result[$setting->getName()] = $setting->getValue();
        }

        return $result;
    }

    public static function createSetting(array $setting) : Setting
    {
        /* Initialize validator */
        if(! isset($settings['validator'])) {
            $settings['validator'] = [];
        }

        $validate = $setting['validate'] ?? null;
        if($validate !== null) {
            $setting['validator']['validate'] = $validate;
        }

        $validateItems = $setting['validate_items'] ?? null;
        if($validateItems !== null) {
            $setting['validator']['validate_items'] = $validateItems;
        }

        return Setting::fromArray($setting);
    }

    abstract static protected function defineSettings() : array;
}
