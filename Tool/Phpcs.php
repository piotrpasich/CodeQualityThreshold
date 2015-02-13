<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

use piotrpasich\CodeQualityThreshold\File\FileLocator;
use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Phpcs extends Tool
{

    protected  $defaultOptions = [
        'directory' => 'app',
        'command'   => 'vendor/squizlabs/php_codesniffer/scripts/phpcs --report=csv',
        'rules'     => 'Config/Phpcs/ruleset.xml',
        'threshold' => 0,
        'timeout'   => 1200
    ];
    
    public function composeCommand()
    {

        return "{$this->composeReportCommand()} | tail -n +2 | wc -l";
    }

    public function composeReportCommand()
    {
        $rules = (new FileLocator())->locateFile($this->configuration['rules']);

        return "{$this->configuration['command']} {$this->configuration['directory']} --standard={$rules}";
    }

    public function getThreshold()
    {
        return isset($this->configuration['threshold']) ? $this->configuration['threshold'] : 0;
    }

    public function getErrorMessage()
    {
        return 'The PHP Code Sniffer threshold is exceeded';
    }

    public function getSuccessMessage()
    {
        return 'The PHP Code Sniffer threshold passed';
    }
}
