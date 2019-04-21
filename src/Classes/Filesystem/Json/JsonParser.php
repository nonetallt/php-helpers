<?php

namespace Nonetallt\Helpers\Filesystem\Json;

use Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException;
use Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException;
use Nonetallt\Helpers\Filesystem\Exceptions\FileNotFoundException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotFileException;
use Nonetallt\Helpers\Filesystem\Exceptions\TargetNotDirectoryException;

/**
 * Simple wrapper class for json_decode and json_encode
 */
class JsonParser
{
    public function __construct()
    {

    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    public function decodeFile(string $filepath)
    {
        try {
            if(! file_exists($filepath)) throw new FileNotFoundException($filepath);
            if(! is_file($filepath)) throw new TargetNotFileException($filepath);

            $result = file_get_contents($filepath);
            if($result === false) throw new FilesystemException("Error reading file", $filepath);

            return $this->decode($result);
        }
        catch(FilesystemException $e) {
            throw new JsonParsingException("Specified file could not be used for parsing", 0, $e);
        }
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    public function encodeIntoFile($value, string $filepath, bool $overwrite = false)
    {
        try {
            if(! file_exists(dirname($filepath))) throw new FileNotFoundException($filepath, 'Parent directory does not exist');
            if(file_exists($filepath) && $overwrite === false) throw new FilesystemException('File already exists', $filepath);
            if(is_dir($filepath)) throw new TargetNotDirectoryException($filepath);

            $result = file_put_contents($filepath, $this->encode($value));
            if($result === false) throw new FilesystemException("Error writing to file", $filepath);
        }
        catch(FilesystemException $e) {
            throw new JsonParsingException("Specified file could not be used for parsing", 0, $e);
        }
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    public function decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        $result = json_decode($json, $assoc, $depth, $options);
        if($result === null) {
            throw new JsonParsingException();
        }

        return $result;
    }

    /**
     * @throws Nonetallt\Helpers\Filesystem\Json\Exceptions\JsonParsingException
     */
    public function encode($value, int $options = 0, int $depth = 512) : string
    {
        $result = json_encode($value, $options, $depth);
        if($result === false) {
            throw new JsonParsingException();
        }

        return $result;
    }
}
