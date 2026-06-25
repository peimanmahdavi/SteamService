<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamUserStats API interface.
 *
 * Lets you fetch achievements and stats for players and games globally.
 */
class UserStatsResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get global achievement unlock percentages across all players for a game.
     *
     * Useful for seeing how rare or common each achievement is worldwide.
     *
     * @param  int  $appId  The Steam AppID of the game
     *
     * @return array  An "achievementpercentages" object with an "achievements" array,
     *                each entry containing "name" and "percent"
     *
     * @throws SteamApiException
     *
     * @example
     *   $data = $steam->userStats()->getGlobalAchievementPercentages(440);
     *   foreach ($data['achievementpercentages']['achievements'] as $achievement) {
     *       echo $achievement['name'] . ': ' . $achievement['percent'] . '%';
     *   }
     */
    public function getGlobalAchievementPercentages(int $appId): object
    {
        return $this->get('ISteamUserStats', 'GetGlobalAchievementPercentagesForApp', SteamEndPoints::VERSION_2, [
            'gameid' => $appId,
        ]);
    }

    /**
     * Get achievement completion status for a specific player in a specific game.
     *
     * @param  string       $steamId   The 64-bit Steam ID of the player
     * @param  int          $appId     The Steam AppID of the game
     * @param  string|null  $language  Language code for localized names (e.g. "en", "de", "fr")
     *
     * @return array  A "playerstats" object with an "achievements" array,
     *                each entry containing apiname, achieved (0 or 1), unlocktime,
     *                and optionally name/description if language is set
     *
     * @throws SteamApiException
     *
     * @example
     *   $data = $steam->userStats()->getPlayerAchievements('76561197960435530', 440, language: 'en');
     *   foreach ($data['playerstats']['achievements'] as $achievement) {
     *       echo $achievement['name'] . ': ' . ($achievement['achieved'] ? 'Unlocked' : 'Locked');
     *   }
     */
    public function getPlayerAchievements(string $steamId, int $appId, ?string $language = null): object
    {
        $params = [
            'steamid' => $steamId,
            'appid'   => $appId,
        ];

        if ($language !== null) {
            $params['l'] = $language;
        }

        return $this->get('ISteamUserStats', 'GetPlayerAchievements', SteamEndPoints::VERSION_1, $params);
    }

    /**
     * Get in-game stats for a specific player in a specific game.
     *
     * Stats are game-defined numeric counters (e.g. kills, hours played, matches won).
     *
     * @param  string       $steamId   The 64-bit Steam ID of the player
     * @param  int          $appId     The Steam AppID of the game
     * @param  string|null  $language  Language code for localized names (e.g. "en", "de")
     *
     * @return array  A "playerstats" object containing a "stats" array (each with name + value)
     *                and an "achievements" array (each with apiname + achieved)
     *
     * @throws SteamApiException
     *
     * @example
     *   $data = $steam->userStats()->getPlayerStats('76561197960435530', 440);
     *   foreach ($data['playerstats']['stats'] as $stat) {
     *       echo $stat['name'] . ': ' . $stat['value'];
     *   }
     */
    public function getPlayerStats(string $steamId, int $appId, ?string $language = null): object
    {
        $params = [
            'steamid' => $steamId,
            'appid'   => $appId,
        ];

        if ($language !== null) {
            $params['l'] = $language;
        }

        return $this->get('ISteamUserStats', 'GetUserStatsForGame', SteamEndPoints::VERSION_2, $params);
    }
}