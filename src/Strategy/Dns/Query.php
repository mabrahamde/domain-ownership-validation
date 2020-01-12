<?php

namespace MabrahamDe\DomainValidation\Strategy\Dns;

/**
 * Class Query
 *
 * Wrapper for Spatie\Dns library needed for testing
 *
 * @package MabrahamDe\DomainValidation\Strategy\Dns
 */
class Query
{
    /**
     * Allowed record-types
     * @var array
     */
    const RECORD_TYPES = [
        'TXT',
        'NS',
    ];


    /**
     * Query constructor.
     */
    public function __construct()
    {

    }


    /**
     * Query a (specific) nameserver for a dns record-type
     *
     * @param string $domain
     * @param string $type
     * @param string $nameserver
     *
     * @return array
     *
     * @throws \Spatie\Dns\Exceptions\CouldNotFetchDns
     */
    public function query($domain, $type, $nameserver='')
    {
        $type = strtoupper($type);

        if (!in_array($type, self::RECORD_TYPES)) {
            throw new \Exception('Unsupported record type');
        }

        $dns = new \Spatie\Dns\Dns(idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46), $nameserver);

        $records = [];
        foreach (explode(PHP_EOL, $dns->getRecords($type)) as $record) {
            if ($parsedRecord = $this->parseRecord($record)) {
                $records[] = $parsedRecord;
            }
        }

        return $records;

    }


    /**
     * Tokenize a dns-record
     *
     * @param string $record
     * @return array|bool
     */
    protected function parseRecord($record)
    {
        if (preg_match('/([^\s]+)\s+([0-9]+)\s+IN\s+([A-Z]+)\s+(.+)/', $record, $matches)) {
            return [
                'name'  => $matches[1],
                'ttl'   => $matches[2],
                'type'  => $matches[3],
                'value' => $matches[4],
            ];
        }

        return false;

    }


}
