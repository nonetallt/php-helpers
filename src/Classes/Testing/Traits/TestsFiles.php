<?php

namespace Nonetallt\Helpers\Testing\Traits;

trait TestsFiles
{
    private $basePath;

    /**
     * @before
     */
    protected function initializeForPhpunit()
    {
        /* $this->cleanOutput(); */
    }

    protected function cleanOutput(string $folder)
    {
        /* $folder = new Directory($folder); */
        /* $folder->removeRecursive(); */
    }

    /**
     * Try to guess the project base path.
     *
     * @throws Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException
     * @return string $path
     * 
     */
    public function guessBasePath() : string
    {
        $path = getcwd();

        if($path === false) {
            $msg = "Could not set base path using getcwd()";
            throw new FilesystemException($msg);
        }

        return $path;
    }

    public function appendToPath(string $path, ?string $append) : string
    {
        if($append !== null && ! starts_with($append, '/')) $append = "/$append";
        return $path . $append;
    }

    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function getBasePath(string $append = null)
    {
        if($this->basePath === null) $this->setBasePath($this->guessBasePath());
        return $this->appendToPath($this->basePath, $append);
    }

    public function getTestPath(string $append = null)
    {
        $choices = ['test', 'tests'];
        $options = [];

        foreach($choices as $dir) {
            $path = $this->getBasePath($dir);
            $options[] = $path;
            if(is_dir($path)) return $this->appendToPath($path, $append);
        }

        $options = implode(PHP_EOL, $options);
        $msg = "Could not find a valid testing directory from following choices:" . PHP_EOL . $options;
        throw new FilesystemException($msg, $this->getBasePath());
    }

    public function getTestInputPath(string $append = null)
    {
        return $this->appendToPath($this->getTestPath('input'), $append);
    }

    public function getTestOutputPath(string $append = null)
    {
        return $this->appendToPath($this->getTestPath('output'), $append);
    }
}
