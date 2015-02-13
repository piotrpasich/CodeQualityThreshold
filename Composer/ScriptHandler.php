<?php

namespace piotrpasich\CodeQualityThreshold\Composer;

use piotrpasich\CodeQualityThreshold\File\FileLocator;
use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\Process\Process;
use Composer\Script\CommandEvent;
use Symfony\Component\Yaml\Parser;


class ScriptHandler
{

    private static $defaultOptions = [
        'default-file' => 'Config/tools.yml',
        'file'         => []
    ];

    /**
     * @param $event CommandEvent A instance
     */
    public static function checkThresholds(CommandEvent $event)
    {
        $options = static::loadConfiguration($event);
        $tools = static::getTools($options);

        foreach ($tools as $toolConfiguration) {
            /** @var \piotrpasich\CodeQualityThreshold\Tool\Tool $tool */
            $tool = new $toolConfiguration['class']($toolConfiguration);

            if (! $tool instanceof Tool) {
                throw new \RuntimeException('The tool should implement Tool abstract class');
            }

            $process = static::runCommand($event, $tool, $tool->composeCommand());

            if (!$process->isSuccessful()) {
                throw new \RuntimeException(sprintf("%s: %s", $process->getErrorOutput(), $process->getOutput()));
            }

            if ((int)$process->getOutput() > $tool->getThreshold()) {
                static::runCommand($event, $tool, $tool->composeReportCommand());

                throw new \RuntimeException(sprintf("%s: %s", $tool->getErrorMessage(), $process->getOutput()));
            }

            $event->getIO()->write("<info>{$tool->getSuccessMessage()}</info>");
        }
    }

    protected static function loadConfiguration(CommandEvent $event)
    {
        $composerConfiguration = $event->getComposer()->getPackage()->getExtra();

        return static::getOptions(isset($composerConfiguration['cqt-parameters']) ? $composerConfiguration['cqt-parameters'] : []);
    }

    protected static function runCommand(CommandEvent $event, Tool $tool, $command)
    {
        if ($event->getIO()->isVerbose()) {
            $event->getIO()->write($command);
        }

        $process = new Process($command, getcwd(), null, null, $tool->getTimeout());
        $process->run(function ($type, $buffer) use ($event) {
            if ($event->getIO()->isVerbose()) {
                $event->getIO()->write($buffer, false);
            }
        });

        return $process;
    }

    protected static function getOptions(array $options)
    {
        return array_merge(static::$defaultOptions, $options);
    }

    protected static function getTools(array $options)
    {
        $tools = static::loadFile($options['default-file']);

        if (isset($options['file']) && is_array($options['file'])) {
            foreach ($options['file'] as $file) {
                $tools = array_merge($tools, static::loadFile($file));
            }
        } else if (isset($options['file']) && is_string($options['file'])) {
            $tools = array_merge($tools, static::loadFile($options['file']));
        }

        return $tools;
    }

    protected static function loadFile($filePath)
    {
        $filePath = (new FileLocator())->locateFile($filePath);

        return (new Parser())->parse(file_get_contents($filePath));
    }
}
