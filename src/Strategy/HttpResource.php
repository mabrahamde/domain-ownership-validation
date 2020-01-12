<?php

namespace MabrahamDe\DomainValidation\Strategy;

use Nyholm\Psr7\Response;
use MabrahamDe\DomainValidation\Model\Strategy;

/**
 * Class HttpResource
 *
 * This validation strategy validates the domain ownership based on individual http resources.
 * The token must be available under an uri specified by domain and validation key.
 *
 * @package MabrahamDe\DomainValidation\Strategy
 */
class HttpResource extends AbstractHttp
{
    /**
     * @var string
     */
    const NAME = 'httpresource';

    /**
     * Check invalid because request for uri was successful, however content is wrong
     * @var string
     */
    const CHECK_INVALID_VALUE = 'wrong';


    /**
     * @param string $domain
     * @param string $token
     *
     * @return \MabrahamDe\DomainValidation\Model\Result|Strategy
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function validate($domain, $token)
    {
        $this->domain = $domain;
        $this->token  = $token;

        $allowedUris = [
            sprintf('http://%s/%s', $domain, $this->validationKey),
            sprintf('http://www.%s/%s', $domain, $this->validationKey),
            sprintf('https://%s/%s', $domain, $this->validationKey),
            sprintf('https://www.%s/%s', $domain, $this->validationKey),
        ];

        $skip = false;
        $validationDetails = [];
        foreach ($allowedUris as $uri) {
            if ($skip) {
                $validationDetails[$uri] = self::CHECK_SKIPPED;

                continue;
            }

            $response = $this->httpClient->sendRequest(new \Nyholm\Psr7\Request('GET', $uri));

            $checkResult = $this->validateHttpResponse($response);
            $validationDetails[$uri] = $checkResult;

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

        return new Strategy(
            self::NAME,
            $this->mode,
            $this->evaluate($validationDetails),
            $validationDetails
        );

    }


    /**
     * Validates the http response in context of validation
     *
     * @param Response $response
     *
     * @return string|null
     */
    protected function validateHttpResponse(Response $response)
    {
        if (null !== $httpValidationResult = $this->validateHttpStatus($response->getStatusCode())) {
            return $httpValidationResult;
        }

        return ($this->token === trim($response->getBody())) ? self::CHECK_VALID : self::CHECK_INVALID_VALUE;

    }


}
