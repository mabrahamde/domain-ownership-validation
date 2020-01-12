<?php

namespace MabrahamDe\DomainValidation\Strategy;

use MabrahamDe\DomainValidation\StrategyInterface;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var string
     */
    const MODE_ALL = 'all';

    /**
     * @var string
     */
    const MODE_ANY = 'any';

    /**
     * @var string
     */
    const CHECK_VALID = 'valid';

    /**
     * @var string
     */
    const CHECK_SKIPPED = 'skipped';

    /**
     * @var string
     */
    protected $validationKey;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $token;


    /**
     * @param array $validationDetails
     *
     * @return bool
     */
    protected function evaluate($validationDetails)
    {
        foreach ($validationDetails as $validationDetail) {
            if (self::CHECK_VALID === $validationDetail && $this->mode = self::MODE_ANY) {
                return true;
            } else if (self::CHECK_VALID !== $validationDetail && self::MODE_ALL === $this->mode) {
                return false;
            }
        }

        return self::MODE_ALL === $this->mode;

    }//end evaluate()


}//end class
