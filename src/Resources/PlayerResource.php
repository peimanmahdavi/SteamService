<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the IPlayerService API interface.
 *
 * Lets you fetch a player's owned games, recently played games, and more.
 */
class PlayerResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get all games owned by a Steam user, along with playtime data.
     *
     * Profile must be public, unless the API key belongs to the same account.
     *
     * @param  string     $steamId              The 64-bit Steam ID of the player
     * @param  bool       $includeGameDetails   Whether to include game name and logo info (default: true)
     * @param  bool       $includeFreeGames     Whether to include free-to-play games the user has played (default: false)
     * @param  int[]|null $filterToAppIds       Optional list of AppIDs to restrict results to
     *
     * @return array  Contains "game_count" and a "games" array with appid, name, playtime_forever, playtime_2weeks, etc.
     *
     * @throws SteamApiException
     *
     * @example
     *   $library = $steam->player()->getOwnedGames('76561197960435530', includeFreeGames: true);
     *   echo $library['response']['game_count'];
     */
    public function getOwnedGames(
        string $steamId,
        bool $includeGameDetails = true,
        bool $includeFreeGames = false,
        ?array $filterToAppIds = null
    ): array {
        $params = [
            'steamid'                  => $steamId,
            'include_appinfo'          => (int) $includeGameDetails,
            'include_played_free_games' => (int) $includeFreeGames,
        ];

        // appids_filter must be passed as JSON (Service interface requirement)
        if (!empty($filterToAppIds)) {
            $params['input_json'] = json_encode(['appids_filter' => $filterToAppIds]);
        }

        return $this->get('IPlayerService', 'GetOwnedGames', 1, $params);
    }

    /**
     * Get games a player has played in the last two weeks.
     *
     * Profile must be public, unless the API key belongs to the same account.
     *
     * @param  string    $steamId  The 64-bit Steam ID of the player
     * @param  int|null  $limit    Max number of games to return (no limit by default)
     *
     * @return array  Contains "total_count" and a "games" array with appid, name, playtime_2weeks, playtime_forever, etc.
     *
     * @throws SteamApiException
     *
     * @example
     *   $recent = $steam->player()->getRecentlyPlayedGames('76561197960435530', limit: 5);
     *   foreach ($recent['response']['games'] as $game) {
     *       echo $game['name'] . ': ' . $game['playtime_2weeks'] . ' minutes';
     *   }
     */
    public function getRecentlyPlayedGames(string $steamId, ?int $limit = null): array
    {
        $params = ['steamid' => $steamId];

        if ($limit !== null) {
            $params['count'] = $limit;
        }

        return $this->get('IPlayerService', 'GetRecentlyPlayedGames', 1, $params);
    }
}