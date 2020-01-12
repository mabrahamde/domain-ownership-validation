<?php

namespace MabrahamDe\DomainValidation;

use MabrahamDe\DomainValidation\Model\Result;

/**
 * Interface StrategyInterface.
 *
 * A validation strategy evaluates the presence of a given token for validation of domain ownership
 */
interface StrategyInterface
{


    /**
     * @param string $domain
     * @param string $token
     *
     * @return Result
     */
    public function validate($domain, $token);


}
