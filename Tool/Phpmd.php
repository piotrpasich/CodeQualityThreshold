<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Phpmd extends Tool
{

    protected  $defaultOptions = [
        'directory' => 'app',
        'rules'     => 'cleancode,codesize,unusedcode',
        'command'   => 'vendor/phpmd/phpmd/src/bin/phpmd',
        'threshold' => 0,
        'timeout'   => 1200
    ];

    public function composeCommand()
    {
        return "{$this->configuration['command']} {$this->configuration['directory']} text {$this->configuration['rules']} | wc -l";
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
