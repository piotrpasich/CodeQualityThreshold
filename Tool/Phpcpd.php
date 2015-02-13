<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

use piotrpasich\CodeQualityThreshold\Tool\Tool;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Phpcpd extends Tool
{

    protected  $defaultOptions = [
        'directory' => 'app',
        'command'   => 'vendor/sebastian/phpcpd/composer/bin/phpcpd',
        'threshold' => 0,
        'timeout'   => 1200
    ];

    public function composeCommand()
    {
        return "{$this->composeReportCommand()} | egrep 'Found [0-9]+ exact clones' -o  | egrep [0-9]+ -o || echo 0";
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
        return 'The PHP Copy Paste Detector threshold is exceeded';
    }

    public function getSuccessMessage()
    {
        return 'The PHP Copy Paste Detector threshold passed';
    }
}
