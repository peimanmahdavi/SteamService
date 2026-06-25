<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamNews API interface.
 *
 * Lets you fetch the latest news entries for any Steam game.
 */
class AppResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * GetServersAtAddress
     *
     * @param  string   $addr       IP Address of Server (IPv4 format)
     * @throws SteamApiException
     */
    public function getServersAtAddress(string $addr): object
    {
        return $this->get('ISteamApps', 'GetServersAtAddress', 'v1',[
            'addr' => $addr,
        ]);
    }

    /**
     * Check if a game install is up-to-date.
     *
     * @param  int  $appid    AppID of the game (uint32)
     * @param  int  $version  The installed version of the game to check (uint32)
     * @return object
     * @throws SteamApiException
     */
    public function UpToDateCheck(int $appid, int $version): object
    {
        return $this->get('ISteamApps', 'UpToDateCheck', 'v1', [
            'appid'   => $appid,
            'version' => $version,
        ]);
    }
}