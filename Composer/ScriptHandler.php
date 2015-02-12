<?php

namespace piotrpasich\CodeQualityThreshold\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;

class ScriptHandler
{

    /**
     * Composer variables are declared static so that an event could update
     * a composer.json and set new options, making them immediately available
     * to forthcoming listeners.
     */
    private static $options = [
        'cqs-config' => 'Config/tools.yml',
    ];

    /**
     * Builds the bootstrap file.
     *
     * The bootstrap file contains PHP file that are always needed by the application.
     * It speeds up the application bootstrapping.
     *
     * @param $event CommandEvent A instance
     */
    public static function checkThresholds(CommandEvent $event = null)
    {
        $options = self::getOptions($event);

        $process = new Process($php.($phpArgs ? ' '.$phpArgs : '').' '.$cmd.' '.$bootstrapDir.' '.$autoloadDir.' '.$useNewDirectoryStructure, getcwd(), null, null, $timeout);
        $process->run(function ($type, $buffer) use ($event) { $event->getIO()->write($buffer, false); });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('An error occurred when generating the bootstrap file.');
        }

    }

    protected static function getOptions(CommandEvent $event)
    {
        return array_merge(self::$options, $event->getComposer()->getPackage()->getExtra());
    }
}
