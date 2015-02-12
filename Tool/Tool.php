<?php

namespace piotrpasich\CodeQualityThreshold\Tool;

abstract class Tool
{

    /**
     * @var array
     */
    protected $configuration;

    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns a string with bash command
     *
     * @return string
     */
    abstract public function composeCommand();

    /**
     * Returns an integer with the threshold
     *
     * @return Integer
     */
    abstract public function getThreshold();

}