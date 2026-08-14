<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamWebAPIUtil API interface.
 *
 * Provides utility methods for discovering available Steam APIs and server info.
 */
class WebAPIUtilResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get a list of all supported API interfaces and methods.
     *
     * @return object The full API listing.
     *
     * @throws SteamApiException
     *
     * @example
     *   $apiList = $steam->webAPIUtil()->getSupportedAPIList();
     */
    public function getSupportedAPIList(): object
    {
        return $this->get('ISteamWebAPIUtil', 'GetSupportedAPIList', SteamEndPoints::VERSION_1);
    }

    /**
     * Get server information (e.g., current time, server time).
     *
     * @return object Server info data.
     *
     * @throws SteamApiException
     *
     * @example
     *   $info = $steam->webAPIUtil()->getServerInfo();
     */
    public function getServerInfo(): object
    {
        return $this->get('ISteamWebAPIUtil', 'GetServerInfo', SteamEndPoints::VERSION_1);
    }
}