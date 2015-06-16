<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Phplint extends Tool
{

    protected $defaultOptions = [
        'directory' => 'src',
        'command'   => "find",
        'threshold' => 0,
        'timeout'   => 1200
    ];

    public function composeCommand()
    {
        return "{$this->composeReportCommand()} | grep -v 'No syntax error' | wc -l";
    }

    public function composeReportCommand()
    {
        return "{$this->configuration['command']} {$this->configuration['directory']} -name '*.php' -exec php -l {} \\;";
    }

    public function getThreshold()
    {
        return isset($this->configuration['threshold']) ? $this->configuration['threshold'] : 0;
    }

    public function getErrorMessage()
    {
        return 'The PHP Lint threshold is exceeded';
    }

    public function getSuccessMessage()
    {
        return 'The PHP Lint threshold passed';
    }
}
