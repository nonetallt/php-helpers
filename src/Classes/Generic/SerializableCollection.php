<?php

namespace Nonetallt\Helpers\Generic;

class SerializableCollection extends Collection
{
    /**
     * Get all items as array
     *
     * @param bool $recursive If true, try converting all child items to arrays
     * calling$r "toArray()" on those items that define it as a method
     *
     * @param bool $preserveKeys Wether the result should preserve original
     * array keys
     *
     * @param array $toArrayArgs toArray() arguments passed to the child instances 
     * if not defined, toArray() will be called with the same arguments as
     * this method
     *
     * @return array $items
     */
    public function toArray(bool $recursive = false, bool $preserveKeys = true, array $toArrayArgs = null) : array
    {
        $items = [];

        foreach($this->items as $key => $item) {

            if($recursive && method_exists($item, 'toArray')) {
                $args = $toArrayArgs ?? [$recursive, $preserveKeys, $toArrayArgs];
                $item = $item->toArray(...$args);
            }

            if($preserveKeys) $items[$key] = $item;
            else $items[] = $item;
        }

        return $items;
    }
}
