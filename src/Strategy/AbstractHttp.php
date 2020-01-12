<?php

namespace MabrahamDe\DomainValidation\Strategy;

use Psr\Http\Client\ClientInterface;

abstract class AbstractHttp extends AbstractStrategy
{
    /**
     * Check invalid because uri is a redirect
     * @var string
     */
    const CHECK_INVALID_REDIRECT = 'redirect';

    /**
     * Check invalid because request for uri causes a 4xx status
     * @var string
     */
    const CHECK_INVALID_BAD_REQUEST = 'bad-request';

    /**
     * Check invalid because request for uri causes a 5xx status
     * @var string
     */
    const CHECK_INVALID_SERVER_ERROR = 'server-error';

    /**
     * Check invalid because request for uri timed out
     * @var string
     */
    const CHECK_INVALID_TIMEOUT = 'timeout';

    /**
     * @var ClientInterface
     */
    protected $httpClient;


    /**
     * HtmlTag constructor.
     *
     * AbstractHttp constructor.
     * @param ClientInterface $httpClient
     * @param string $validationKey
     * @param string $mode
     */
    public function __construct(ClientInterface $httpClient, $validationKey='dv', $mode=self::MODE_ANY)
    {
        $this->httpClient    = $httpClient;
        $this->validationKey = $validationKey;
        $this->mode          = $mode;

    }


    /**
     * Validates the http-status of the http-response
     *
     * @param int $status
     *
     * @return string|null
     */
    protected function validateHttpStatus($status)
    {
        $statusFamily = (int) floor($status / 100);

        if (2 === $statusFamily) {
            return null;
        } else if (3 === $statusFamily) {
            return self::CHECK_INVALID_REDIRECT;
        } else if (4 === $statusFamily) {
            return self::CHECK_INVALID_BAD_REQUEST;
        } else if (5 === $statusFamily) {
            return self::CHECK_INVALID_SERVER_ERROR;
        }

        return self::CHECK_INVALID_TIMEOUT;

    }


}
