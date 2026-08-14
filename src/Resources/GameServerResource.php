<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamGameServer API interface.
 *
 * Provides methods for querying game server status and accounts.
 */
class GameServerResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get SteamIDs associated with a given IP address.
     *
     * @param string $serverIP The IP address of the game server.
     *
     * @return object Contains server SteamIDs.
     *
     * @throws SteamApiException
     *
     * @example
     *   $servers = $steam->gameServer()->getServerSteamIDsByIP('192.168.1.100');
     */
    public function getServerSteamIDsByIP(string $serverIP): object
    {
        return $this->get('ISteamGameServer', 'GetServerSteamIDsByIP', SteamEndPoints::VERSION_1, [
            'server_ip' => $serverIP,
        ]);
    }

    /**
     * Get the current status of all game servers.
     *
     * @return object Server status data.
     *
     * @throws SteamApiException
     *
     * @example
     *   $status = $steam->gameServer()->getGameServersStatus();
     */
    public function getGameServersStatus(): object
    {
        return $this->get('ISteamGameServer', 'GetGameServersStatus', SteamEndPoints::VERSION_1);
    }
}