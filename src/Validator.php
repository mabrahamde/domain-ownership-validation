<?php

namespace MabrahamDe\DomainValidation;

/**
 * Class Validator.
 *
 * Validates the domain ownership using given strategies like dns, html-tag or http-resouce
 */
class Validator
{

    /**
     * @var array
     */
    protected $strategies;


    /**
     * Validator constructor.
     *
     * @param array $strategies
     */
    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;

    }


    /**
     * Validates if a given token is published in context of a domain.
     *
     * @param string $domain
     * @param string $token
     * @param array  $results
     *
     * @return bool
     */
    public function validate($domain, $token, &$results=[])
    {
        $valid   = false;
        $results = [];

        // @var StrategyInterface $validationStrategy
        foreach ($this->strategies as $validationStrategy) {
            $result = $validationStrategy->validate($domain, $token);

            if ($result->isValid()) {
                $valid = true;
            }

            $results[] = $result;
        }

        return $valid;

    }


}
