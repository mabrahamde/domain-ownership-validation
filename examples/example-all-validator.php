<?php

require_once '../vendor/autoload.php';



$validator = new \MabrahamDe\DomainValidation\Validator([
    new \MabrahamDe\DomainValidation\Strategy\Dns(
        new \MabrahamDe\DomainValidation\Strategy\Dns\Query()
    ),
    new \MabrahamDe\DomainValidation\Strategy\HtmlTag(
        new \Buzz\Client\MultiCurl(new \Nyholm\Psr7\Factory\Psr17Factory(), ['verify' => false])
    ),
    new \MabrahamDe\DomainValidation\Strategy\HttpResource(
        new \Buzz\Client\MultiCurl(new \Nyholm\Psr7\Factory\Psr17Factory(), ['verify' => false])
    ),
]);


$valid = $validator->validate('sitelabs.de', 'foobar', $results);
print_r($results);

if ($valid) {
    echo 'valid';
} else {
    echo 'invalid';
}

echo PHP_EOL;



