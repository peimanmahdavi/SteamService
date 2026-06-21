<?php
namespace Zedx\SteamService\Resources;

use Zedx\SteamService\Concerns\MakesHttpRequests;
use Zedx\SteamService\Exceptions\SteamApiException;

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
     * @param  string|array  $steamIds  A single Steam ID or an array of Steam IDs (max 100)
     *
     * @return array The "players" array from Steam, each entry containing profile fields
     *
     * @throws SteamApiException
     *
     * @example
     *   // Single player
     *   $profile = $steam->users()->getProfiles('76561197960435530');
     *
     *   // Multiple players
     *   $profiles = $steam->users()->getProfiles(['76561197960435530', '76561197960287930']);
     */
    public function getProfiles(string|array $steamIds): array
    {
        $ids = is_array($steamIds) ? implode(',', $steamIds) : $steamIds;

        return $this->get('ISteamUser', 'GetPlayerSummaries', 2, [
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
    public function getFriends(string $steamId, string $filterBy = 'friend'): array
    {
        return $this->get('ISteamUser', 'GetFriendList', 1, [
            'steamid'      => $steamId,
            'relationship' => $filterBy,
        ]);
    }
}