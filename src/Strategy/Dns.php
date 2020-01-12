<?php

namespace MabrahamDe\DomainValidation\Strategy;

use MabrahamDe\DomainValidation\Model\Strategy;
use MabrahamDe\DomainValidation\Strategy\Dns\Query;

/**
 * Class Dns
 *
 * This validation strategy validates the domain ownership based on dns txt-records
 * A TXT-record must be available under the domain. This record must contain <validation_key>=<token> as value.
 *
 * @package MabrahamDe\DomainValidation\Strategy
 */
class Dns extends AbstractStrategy
{
    /**
     * @var string
     */
    const NAME = 'dns';

    /**
     * Invalid because query for ns records of the domain failed
     * @var string
     */
    const CHECK_INVALID_DNS = 'invalid-dns';

    /**
     * Invalid because the TXT-records contains the wrong token
     * @var string
     */
    const CHECK_INVALID_RECORD_VALUE = 'invalid-record-wrong';

    /**
     * Invalid because the TXT-record is missing.
     * @var string
     */
    const CHECK_INVALID_RECORD_MISSING = 'invalid-record-missing';

    /**
     * Injected DNS query library
     * @var Query
     */
    protected $dnsQuery;


    /**
     * Dns constructor.
     *
     * @param Query $dnsQuery
     * @param string $validationKey
     * @param string $mode
     */
    public function __construct(Query $dnsQuery, $validationKey='dv', $mode=self::MODE_ANY)
    {
        $this->dnsQuery      = $dnsQuery;
        $this->validationKey = $validationKey;
        $this->mode          = $mode;

    }


    /**
     * @param string $domain
     * @param string $token
     *
     * @return Strategy
     */
    public function validate($domain, $token)
    {
        $this->domain = $domain;
        $this->token  = $token;

        $nameservers = $this->getNs();

        if (empty($nameservers)) {
            $validationDetails[] = self::CHECK_INVALID_DNS;
        } else {
            $validationDetails = [];

            $skip = false;
            foreach ($nameservers as $nameserver) {
                if ($skip) {
                    $validationDetails[$nameserver] = self::CHECK_SKIPPED;

                    continue;
                }

                $checkResult = $this->validateDns($nameserver);
                $validationDetails[$nameserver] = $checkResult;

                if (self::CHECK_VALID === $checkResult) {
                    if (self::MODE_ANY === $this->mode) {
                        $skip = true;
                    }
                } else {
                    if (self::MODE_ALL === $this->mode) {
                        $skip = true;
                    }
                }
            }
        }

        return new Strategy(
            self::NAME,
            $this->mode,
            $this->evaluate($validationDetails),
            $validationDetails
        );

    }


    protected function validateDns($nameserver)
    {
        $dnsTokens = $this->getTokensFromDns($nameserver);

        if (empty($dnsTokens)) {
            return self::CHECK_INVALID_RECORD_MISSING;
        }

        if (in_array($this->token, $dnsTokens)) {
            return self::CHECK_VALID;
        }

        return self::CHECK_INVALID_RECORD_VALUE;

    }//end validateDns()


    protected function getTokensFromDns($nameserver)
    {
        $tokenRecords = [];

        foreach ($this->dnsQuery->query($this->domain, 'txt', $nameserver) as $record) {
            if (preg_match('/"'.preg_quote($this->validationKey, '/').'=([A-Za-z0-9]+)"$/', $record['value'], $matches)) {
                $tokenRecords[] = $matches[1];
            }
        }

        return $tokenRecords;

    }


    protected function getNs()
    {
        $nameservers = [];
        foreach ($this->dnsQuery->query($this->domain, 'ns') as $nsRecord) {
            $nameservers[] = $nsRecord['value'];
        }

        return $nameservers;

    }


}
