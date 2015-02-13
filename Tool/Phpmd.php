<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

use piotrpasich\CodeQualityThreshold\File\FileLocator;
use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Phpmd extends Tool
{

    protected  $defaultOptions = [
        'directory' => 'app',
        'rules'     => 'cleancode,codesize,unusedcode',
        'command'   => 'php vendor/phpmd/phpmd/src/bin/phpmd',
        'threshold' => 0,
        'timeout'   => 1200
    ];

    public function composeCommand()
    {
        return "{$this->composeReportCommand()} | wc -l";
    }

    public function composeReportCommand()
    {
        $rules = $this->configuration['rules'];
        if ('xml' == pathinfo($rules, PATHINFO_EXTENSION)) {
            $rules = (new FileLocator())->locateFile($rules);
        }

        return "{$this->configuration['command']} {$this->configuration['directory']} text {$rules}";
    }

    public function getThreshold()
    {
        return isset($this->configuration['threshold']) ? $this->configuration['threshold'] : 0;
    }

    public function getErrorMessage()
    {
        return 'The PHP MD threshold is exceeded';
    }

    public function getSuccessMessage()
    {
        return 'The PHP MD threshold passed';
    }
}
