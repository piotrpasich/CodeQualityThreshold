<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Phpdcd extends Tool
{

    protected  $defaultOptions = [
        'directory' => 'app',
        'command'   => 'vendor/sebastian/phpdcd/phpdcd',
        'threshold' => 0,
        'timeout'   => 1200
    ];

    public function composeCommand()
    {
        return "{$this->composeReportCommand()} | grep LOC | wc -l";
    }

    public function composeReportCommand()
    {
        return "{$this->configuration['command']} {$this->configuration['directory']}";
    }

    public function getThreshold()
    {
        return isset($this->configuration['threshold']) ? $this->configuration['threshold'] : 0;
    }

    public function getErrorMessage()
    {
        return 'The PHP Dead Code Detector threshold is exceeded';
    }

    public function getSuccessMessage()
    {
        return 'The PHP Dead Code Detector threshold passed';
    }
}
