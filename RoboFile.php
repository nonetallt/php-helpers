<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    // define public methods as commands

    public function watchTests()
    {
        $this->taskWatch()
            ->monitor(['src', 'test/Feature', 'test/Unit'], function() {
                echo 'test';
                $this->taskExec('phpunit')->run();
            })->run();
    }
}
