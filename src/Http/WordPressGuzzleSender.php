<?php

namespace Concept7\WordPressKite\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Saloon\Config;
use Saloon\Http\Senders\GuzzleSender;

class WordPressGuzzleSender extends GuzzleSender
{
    protected function createGuzzleClient(): GuzzleClient
    {
        $this->handlerStack = $this->defaultHandlerStack();

        $options = [
            RequestOptions::CONNECT_TIMEOUT => Config::$defaultConnectionTimeout,
            RequestOptions::TIMEOUT => Config::$defaultRequestTimeout,
            RequestOptions::HTTP_ERRORS => true,
            'handler' => $this->handlerStack,
        ];

        // CRYPTO_METHOD was added in Guzzle 7.5. Another WordPress plugin may have
        // loaded an older Guzzle version first, making this constant undefined.
        if (defined('GuzzleHttp\RequestOptions::CRYPTO_METHOD')) {
            $options[RequestOptions::CRYPTO_METHOD] = Config::$defaultTlsMethod;
        }

        return new GuzzleClient($options);
    }
}
