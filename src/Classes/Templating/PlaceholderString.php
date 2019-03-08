<?php

namespace Nonetallt\Helpers\Templating;

class PlaceholderString
{
    private $content;
    private $parent;

    public function __construct(string $content, PlaceholderString $parent = null)
    {
        $this->content = $content;
        $this->parent = $parent;
    }

    public static function fromNestedString(string $nested, PlaceholderFormat $format) : PlaceholderString
    {
        $placeholders = $format->getPlaceholdersInString($nested);
        if(empty($placeholders)) return new PlaceholderString($nested);

        /* Create first string */
        $str = new PlaceholderString($placeholders[0]);

        if(count($placeholders) < 2) return $str;

        for($n = 1; $n < count($placeholders); $n++) {
            $str = new PlaceholderString($placeholders[$n], $str);
        }

        /* Create first string */
        /* $lastIndex = count($placeholders) - 1; */
        /* $str = new PlaceholderString($placeholders[$lastIndex]); */

        /* if($lastIndex - 1 < 0) return $str; */
        /* for($n = $lastIndex -1; $n >= 0; $n--) { */
        /*     $str = new PlaceholderString($placeholders[$n], $str); */
        /* } */

        return $str;
    }

    public function __toString()
    {
        $result = $this->loop(function($item, $level) {
            return $item->getContent();
        });

        return implode(PHP_EOL, $result);
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function loop(callable $cb, array &$result = [], int $level = 1)
    {
        $result[] = $cb($this, $level);

        if($this->parent !== null) {
            $this->parent->loop($cb, $result, $level + 1);
        }

        return $result;
    }

    public function replace(array $placeholders, PlaceholderFormat $format)
    {
        foreach($placeholders as $placeholder => $value) {
            $this->content = str_replace($placeholder, $value, $this->content);
        }
        if($this->parent !== null) $this->parent->replace($placeholders, $format);
    }

    public function getDepth()
    {
        $result = [];
        $this->loop(function($item) {
            return true;
        }, $result);

        return count($result);
    }
}
