<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamLeaderboards API interface.
 *
 * Provides methods for fetching leaderboard definitions and entries.
 */
class LeaderboardResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get definitions of all leaderboards for a given game.
     *
     * @param int $appId The Steam AppID of the game.
     *
     * @return object Leaderboard definitions.
     *
     * @throws SteamApiException
     *
     * @example
     *   $defs = $steam->leaderboards()->getLeaderboardDefinitions(440);
     */
    public function getLeaderboardDefinitions(int $appId): object
    {
        return $this->get('ISteamLeaderboards', 'GetLeaderboardDefinitions', SteamEndPoints::VERSION_1, [
            'appid' => $appId,
        ]);
    }

    /**
     * Get leaderboard entries for a specific leaderboard.
     *
     * @param int    $appId          The Steam AppID of the game.
     * @param int    $leaderboardId  The ID of the leaderboard.
     * @param int    $start          Starting rank (1-based).
     * @param int    $end            Ending rank (inclusive).
     * @param string $steamId        Optional SteamID to filter entries for a specific user.
     *
     * @return object Leaderboard entries.
     *
     * @throws SteamApiException
     *
     * @example
     *   $entries = $steam->leaderboards()->getLeaderboardEntries(440, 12345, 1, 10);
     */
    public function getLeaderboardEntries(int $appId, int $leaderboardId, int $start, int $end, ?string $steamId = null): object
    {
        $params = [
            'appid'          => $appId,
            'leaderboardid'  => $leaderboardId,
            'start'          => $start,
            'end'            => $end,
        ];

        if ($steamId !== null) {
            $params['steamid'] = $steamId;
        }

        return $this->get('ISteamLeaderboards', 'GetLeaderboardEntries', SteamEndPoints::VERSION_1, $params);
    }
}