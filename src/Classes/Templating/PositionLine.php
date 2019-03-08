<?php

namespace Nonetallt\Helpers\Templating;

use Nonetallt\Helpers\Arrays\TypedArray;

/* TODO check that there is an equal amount of stops and starts */
class PositionLine
{
    private $positions;

    public function __construct(array $positions)
    {
        $this->positions = TypedArray::create(Position::class, $positions);

        usort($this->positions, function($a, $b) {
            return $a->getPosition() <=> $b->getPosition();
        });
    }

    public static function fromArray(array $entries)
    {
        $items = [];
        foreach($entries as $entry) {
            $items[] = Position::fromArray($entry);
        }

        return new self($items);
    }

    public function filterType(string $type)
    {
        return array_filter($this->positions, function($pos) use($type){
            return $pos->getType() === $type;
        });
    }

    public function getPairs(bool $reverse = true)
    {
        $ends = $this->filterType('end');
        $starts = $this->filterType('start');
        $pairs = [];

        foreach($starts as $index => $start) {
            $startingPosition = $start->getPosition();
            $items = array_filter($this->positions, function($item) use($startingPosition){
                return $item->getPosition() > $startingPosition;
            });

            $skips = 0;
            foreach($items as $item) {
                if($item->getType() === 'end' && $skips === 0) {
                    break;
                }
                if($item->getType() === 'start') $skips += 2;
            }

            $end = $items[$index + $skips + 1];
            $pairs[$index]['start'] = $startingPosition;
            $pairs[$index]['end'] = $end->getPosition();
        }

        if($reverse) return array_reverse($pairs);
        return $pairs;
    }
}
