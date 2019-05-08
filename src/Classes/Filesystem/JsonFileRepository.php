<?php

namespace Nonetallt\Helpers\Filesystem;

use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;
use Nonetallt\Helpers\Generic\Collection;
use Nonetallt\Helpers\Filesystem\Traits\FindsFiles;
use Nonetallt\Helpers\Filesystem\Json\JsonParser;

class JsonFileRepository extends Collection
{
    use FindsFiles;

    private $dirPath;

    public function __construct(string $dirPath, ?string $itemClass = null)
    {
        $this->setDirPath($dirPath);
        parent::__construct([], $itemClass);
        $this->loadInformation();
    }

    private function loadInformation() : array
    {
        $files = $this->findFilesWithExtension($this->dirPath, 'json');
        $parser = new JsonParser();
        $info = [];

        foreach($files as $file) {
            $filepath = "$this->dirPath/$file";
            $decoded = $parser->decodeFile($filepath, true);
            $info[] = $this->loadFile($filepath, $decoded);
        }

        return $info;
    }

    public function setDirPath(string $dirPath)
    {
        if(! file_exists($dirPath)) throw new FileNotFoundException($dirPath);
        if(! is_dir($dirPath)) throw new TargetNotDirectoryException($dirPath);

        $this->dirPath = $dirPath;
    }

    public function toArray() : array
    {
        return $this->map(function($item) {
            return $item->toArray();
        });
    }

    protected function loadFile(string $filepath, array $data)
    {
        $this->push($data);
    }
}
