<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamMatchmaking API interface.
 *
 * Provides methods for querying lobby lists and data.
 */
class MatchmakingResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get a list of lobbies for a given game.
     *
     * @param int     $appId    The Steam AppID of the game.
     * @param int     $max      Maximum number of lobbies to return.
     * @param string  $filter   Optional filter string (e.g., "gamedir:tf").
     *
     * @return object Lobby list.
     *
     * @throws SteamApiException
     *
     * @example
     *   $lobbies = $steam->matchmaking()->getLobbyList(440, 5);
     */
    public function getLobbyList(int $appId, int $max = 10, ?string $filter = null): object
    {
        $params = [
            'appid' => $appId,
            'max'   => $max,
        ];

        if ($filter !== null) {
            $params['filter'] = $filter;
        }

        return $this->get('ISteamMatchmaking', 'GetLobbyList', SteamEndPoints::VERSION_1, $params);
    }

    /**
     * Get data for a specific lobby.
     *
     * @param string $lobbyId The 64-bit lobby ID.
     *
     * @return object Lobby data.
     *
     * @throws SteamApiException
     *
     * @example
     *   $data = $steam->matchmaking()->getLobbyData('123456789012345678');
     */
    public function getLobbyData(string $lobbyId): object
    {
        return $this->get('ISteamMatchmaking', 'GetLobbyData', SteamEndPoints::VERSION_1, [
            'lobbyid' => $lobbyId,
        ]);
    }
}