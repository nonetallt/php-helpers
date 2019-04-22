<?php

namespace Nonetallt\Helpers\Internet\Routing;

use Nonetallt\Helpers\Templating\PlaceholderFormat;

class Router
{
    private $urlPlaceholderFormat;

    public function __construct(string $urlPlaceholderFormat = '{$}')
    {
        $this->setUrlPlaceholderFormat($urlPlaceholderFormat);
    }

    public function setUrlPlaceholderFormat(string $urlPlaceholderFormat)
    {
        $this->urlPlaceholderFormat = new PlaceholderFormat($urlPlaceholderFormat);
    }

    public function parseUrl(string $url, array $parameters = [])
    {
        foreach($this->urlPlaceholderFormat->getPlaceholdersInString($url) as $placeholder) {
            $key = $this->urlPlaceholderFormat->trimPlaceholderString($placeholder);
            $value = $parameters[$key] ?? null;
            if($value === null) throw new \InvalidArgumentException("Missing required url parameter '$key'");
            $url = str_replace($placeholder, $value, $url);
        }

        return $url;
    }
}
