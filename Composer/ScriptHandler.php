<?php

namespace piotrpasich\CodeQualityThreshold\Composer;

use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;
use Composer\IO\IOInterface;
use Symfony\Component\Yaml\Inline;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;


class ScriptHandler
{

    /**
     * Default configuration. Can be overwritten by composer extra options;
     */
    private static $defaultOptions = [
        'default-file' => 'Config/tools.yml',
        'file'         => []
    ];

    /**
     * @param $event CommandEvent A instance
     */
    public static function checkThresholds(CommandEvent $event)
    {
        $options = self::loadConfiguration($event);

        $tools = self::getTools($options);

        foreach ($tools as $toolConfiguration) {
            /** @var \piotrpasich\CodeQualityThreshold\Tool\Tool $tool */
            $tool = new $toolConfiguration['class']($toolConfiguration);

            if (! $tool instanceof Tool) {
                throw new \RuntimeException('The tool should implement Tool abstract class');
            }

            $process = new Process($tool->composeCommand(), getcwd(), null, null, $tool->getTimeout());
            $process->run(function ($type, $buffer) use ($event) { $event->getIO()->write($buffer, false); });

            if (!$process->isSuccessful()) {
                throw new \RuntimeException(sprintf("%s: %s", $process->getErrorOutput(), $process->getOutput()));
            }

            if ((int)$process->getOutput() > $tool->getThreshold()) {
                throw new \RuntimeException(sprintf("%s: %s", $tool->getErrorMessage(), $process->getOutput()));
            }

            $event->getIO()->write($tool->getSuccessMessage());
        }
    }

    public static function loadConfiguration(CommandEvent $event)
    {
        $composerConfiguration = $event->getComposer()->getPackage()->getExtra();

        return self::getOptions(isset($composerConfiguration['cqt-parameters']) ? $composerConfiguration['cqt-parameters'] : []);
    }

    protected static function getOptions(array $options)
    {
        return array_merge(self::$defaultOptions, $options);
    }

    protected static function getTools(array $options)
    {
        $tools = self::loadFile($options['default-file'], __DIR__ . '/../');

        if (isset($options['file']) && is_array($options['file'])) {
            foreach ($options['file'] as $file) {
                $tools = array_merge($tools, self::loadFile($file));
            }
        } else if (isset($options['file']) && is_string($options['file'])) {
            $tools = array_merge($tools, self::loadFile($options['file']));
        }

        return $tools;
    }

    protected static function loadFile($filePath, $startDir = '')
    {
        return (new Parser())->parse(file_get_contents($startDir . $filePath));
    }
}
