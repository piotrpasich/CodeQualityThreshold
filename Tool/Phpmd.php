<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

use piotrpasich\CodeQualityThreshold\Tool\Tool;

class Phpmd extends Tool
{

    public function composeCommand()
    {
        return 'vendor/phpmd/phpmd/src/bin/phpmd tests/acceptance/ text cleancode,codesize,unusedcode';

    }

    public function getThreshold()
    {

    }

}