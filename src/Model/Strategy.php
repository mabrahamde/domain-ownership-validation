<?php

namespace MabrahamDe\DomainValidation\Model;

/**
 * Class Strategy
 *
 * Result of domain validation using single strategy
 *
 * @package MabrahamDe\DomainValidation\Model
 */
final class Strategy
{

    /**
     * Name of strategy
     * @var string
     */
    protected $name;

    /**
     * Used validation mode
     * @var string
     */
    protected $mode;

    /**
     * Is ownership validated sucessfully
     * @var boolean
     */
    protected $valid;

    /**
     * Detailed validation results
     * @var array
     */
    protected $details;


    /**
     * Strategy constructor.
     *
     * @param string $name
     * @param string $mode
     * @param bool   $valid
     * @param array  $details
     */
    public function __construct($name, $mode, $valid, $details)
    {
        $this->name    = $name;
        $this->mode    = $mode;
        $this->valid   = $valid;
        $this->details = $details;

    }


    /**
     * Get name of validation stragey
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;

    }


    /**
     * Get validation mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;

    }


    /**
     * Is validation sucessful
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;

    }


    /**
     * Get validation details
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->details;

    }


}
