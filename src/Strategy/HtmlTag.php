<?php

namespace MabrahamDe\DomainValidation\Strategy;

use Nyholm\Psr7\Response;
use MabrahamDe\DomainValidation\Model\Strategy;

/**
 * Class HtmlTag
 *
 * This validation strategy validates the domain ownership based on html tags.
 * A meta tag must be available on the start page of a domain. The meta tag must contain the validation key as type
 * and the token as value
 *
 * @package MabrahamDe\DomainValidation\Strategy
 */
class HtmlTag extends AbstractHttp
{
    /**
     * Name of strategy
     *
     * @var string
     */
    const NAME = 'htmltag';

    /**
     * Check invalid because value of html tag is wrong
     *
     * @var string
     */
    const CHECK_INVALID_TAG_VALUE = 'wrong';

    /**
     * Check invalid because html tag ist missing
     * @var string
     */
    const CHECK_INVALID_TAG_MISSING = 'missing';


    /**
     * @param string $domain
     * @param string $token
     *
     * @return \MabrahamDe\DomainValidation\Model\Result|Strategy
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function validate($domain, $token)
    {
        $this->domain = $domain;
        $this->token  = $token;

        $allowedUris = [
            sprintf('http://%s/', $domain),
            sprintf('http://www.%s/', $domain),
            sprintf('https://%s/', $domain),
            sprintf('https://www.%s/', $domain),
        ];

        $skip = false;
        $validationDetails = [];
        foreach ($allowedUris as $uri) {
            if (true === $skip) {
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
     * @return string
     */
    protected function validateHttpResponse(Response $response)
    {
        if (null !== $httpValidationResult = $this->validateHttpStatus($response->getStatusCode())) {
            return $httpValidationResult;
        }

        $tokens = $this->extractTokens($response->getBody());
        if (true === empty($tokens)) {
            return self::CHECK_INVALID_TAG_MISSING;
        }

        return true === in_array($this->token, $tokens) ? self::CHECK_VALID : self::CHECK_INVALID_TAG_VALUE;

    }


    /**
     * Extracts the tokens from meta tags
     *
     * @param string $html
     *
     * @return array
     */
    protected function extractTokens($html)
    {
        $tokens = [];

        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $metas = $doc->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; ++$i) {
            $meta = $metas->item($i);
            if ($meta->getAttribute('name') == $this->validationKey) {
                $tokens[] = $meta->getAttribute('content');
            }
        }

        return $tokens;

    }


}
