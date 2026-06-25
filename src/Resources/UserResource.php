<?php
namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamUser API interface.
 *
 * Lets you fetch player profiles and friend lists.
 */
class UserResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get public profile information for one or more Steam users.
     *
     * Returns data like display name, avatar, online status, current game,
     * and location — depending on each user's privacy settings.
     *
     * @param string|array $steamIds A single Steam ID or an array of Steam IDs (max 100)
     *
     * @return object The "players" array from Steam, each entry containing profile fields
     *
     * @example
     *   // Single player
     *   $profile = $steam->users()->getProfiles('76561197960435530');
     *
     *   // Multiple players
     *   $profiles = $steam->users()->getProfiles(['76561197960435530', '76561197960287930']);
     */
    public function getProfiles(string|array $steamIds): object
    {
        $ids = is_array($steamIds) ? implode(',', $steamIds) : $steamIds;

        return $this->get('ISteamUser', 'GetPlayerSummaries', SteamEndPoints::VERSION_2, [
            'steamids' => $ids,
        ]);
    }

    /**
     * Get the friend list of a Steam user.
     *
     * Only works if the user's Steam Community profile is set to public.
     *
     * @param  string  $steamId      The 64-bit Steam ID of the user
     * @param  string  $filterBy     What relationship to show: "all" or "friend" (default: "friend")
     *
     * @return array  The "friendslist" array, each entry with steamid, relationship, and friend_since timestamp
     *
     * @throws SteamApiException
     *
     * @example
     *   $friends = $steam->users()->getFriends('76561197960435530');
     *   foreach ($friends['friendslist']['friends'] as $friend) {
     *       echo $friend['steamid'];
     *   }
     */
    public function getFriends(string $steamId, string $filterBy = 'friend'): object
    {
        return $this->get('ISteamUser', 'GetFriendList', SteamEndPoints::VERSION_1, [
            'steamid'      => $steamId,
            'relationship' => $filterBy,
        ]);
    }

    /**
     * Retrieve VAC, game, and community ban information for one or more players.
     *
     * Returns ban details such as VAC bans, game bans, economy bans,
     * and the number of days since the last ban for the specified Steam accounts.
     *
     * @param  array $steamIds List of SteamID64 values to query (uint64[])
     * @return object
     * @throws SteamApiException
     */
    public function GetPlayerBans(array $steamIds): object
    {
        return $this->get(
            'ISteamUser',
            'GetPlayerBans',
            'v1',
            [
                'steamids' => implode(',', $steamIds),
            ]
        );
    }
    /**
     * Retrieve the Steam groups a user belongs to.
     *
     * Returns a list of Steam groups associated with the specified
     * Steam account.
     *
     * @param  string $steamId SteamID64 of the user to query (uint64)
     * @return object
     * @throws SteamApiException
     */
    public function GetUserGroupList(string $steamId): object
    {
        return $this->get(
            'ISteamUser',
            'GetUserGroupList',
            'v1',
            [
                'steamid' => $steamId,
            ]
        );
    }
    /**
     * Resolve a Steam vanity URL to a SteamID64.
     *
     * Converts a custom Steam Community profile URL name into the
     * corresponding SteamID64.
     *
     * Example:
     * https://steamcommunity.com/id/gabelogannewell
     * → vanity URL: "gabelogannewell"
     *
     * @param  string $vanityUrl Custom Steam Community URL name
     * @return object
     * @throws SteamApiException
     */
    public function ResolveVanityURL(string $vanityUrl): object
    {
        return $this->get(
            'ISteamUser',
            'ResolveVanityURL',
            SteamEndPoints::VERSION_1,
            [
                'vanityurl' => $vanityUrl,
            ]
        );
    }
}