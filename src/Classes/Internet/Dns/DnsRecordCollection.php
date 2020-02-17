<?php

namespace Nonetallt\Helpers\Internet\Dns;

use Nonetallt\Helpers\Generic\Collection;

class DnsRecordCollection extends Collection
{
    CONST COLLECTION_TYPE = DnsRecord::class;

    public function add(DnsRecord $record) 
    {
        $this->items[] = $record;
    }

    public function recordExists(string $type, string $value)
    {
        foreach($this->ofType($type) as $record) {
            if($record->getValue() === $value) return true;
        }
        return false;
    }

    public function ofType(string $type)
    {
        return array_filter($this->items, function($item) use($type){
            return $item->getType() === $type;
        });
    }

    /**
     * @override
     */
    public function toArray() : array
    {
        $array = [];

        foreach($this->items as $item) {
            $array[$item->getType()] = $item->getValue();
        }

        return $array;
    }
}
