<?php

namespace piotrpasich\CodeQualityThreshold\File;

use Symfony\Component\Config\FileLocator as SymfonyFileLocator;

class FileLocator
{

    public function locateFile($filePath)
    {
        $fileLocator = new SymfonyFileLocator(['.', __DIR__ . '/..']);

        return $fileLocator->locate($filePath);
    }

}
