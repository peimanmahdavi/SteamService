<?php

namespace PmZedx\SteamService;

use PmZedx\SteamService\Exceptions\SteamApiException;
use PmZedx\SteamService\Resources\AppResource;
use PmZedx\SteamService\Resources\EconomyResource;
use PmZedx\SteamService\Resources\GameServerResource;
use PmZedx\SteamService\Resources\LeaderboardResource;
use PmZedx\SteamService\Resources\MatchmakingResource;
use PmZedx\SteamService\Resources\NewsResource;
use PmZedx\SteamService\Resources\PlayerResource;
use PmZedx\SteamService\Resources\PublishedFileResource;
use PmZedx\SteamService\Resources\UserAuthResource;
use PmZedx\SteamService\Resources\UserResource;
use PmZedx\SteamService\Resources\UserStatsResource;
use PmZedx\SteamService\Resources\WebAPIUtilResource;

/**
 * SteamClient — the main entry point for the Steam Web API package.
 *
 * The API key is read automatically from the STEAM_API_KEY environment
 * variable, but you can also pass it directly when constructing the client.
 *
 * ──────────────────────────────────────────────
 * Usage via environment variable (recommended):
 * ──────────────────────────────────────────────
 *   // In your .env or server environment:
 *   // STEAM_API_KEY=your_key_here
 *
 *   $steam = new SteamClient();
 *
 * ─────────────────────────────────────────────
 * Usage by passing the key directly:
 * ─────────────────────────────────────────────
 *   $steam = new SteamClient('your_api_key_here');
 *
 * ─────────────────────────────────
 * Fetching data examples:
 * ─────────────────────────────────
 *   // Get a player's profile
 *   $profile = $steam->users()->getProfiles('76561197960435530');
 *
 *   // Get a player's owned games
 *   $games = $steam->player()->getOwnedGames('76561197960435530');
 *
 *   // Get TF2 news
 *   $news = $steam->news()->getForGame(440, count: 5);
 *
 *   // Get achievement unlock rates for a game
 *   $rates = $steam->userStats()->getGlobalAchievementPercentages(440);
 */
class SteamClient
{
    protected string $apiKey;

    /**
     * Create a new SteamClient instance.
     *
     * @param  string|null  $apiKey  Your Steam Web API key. If null, the client will
     *                               read from the STEAM_API_KEY environment variable.
     *
     * @throws SteamApiException  If no API key is found from either source.
     */
    public function __construct(?string $apiKey = null)
    {
        $resolved = $apiKey ?? getenv('STEAM_API_KEY');

        if (empty($resolved)) {
            throw new SteamApiException(
                'No Steam API key provided. Pass it to the constructor or set the STEAM_API_KEY environment variable.'
            );
        }

        $this->apiKey = $resolved;
    }

    /**
     * Access news-related API methods (ISteamNews).
     *
     * Fetch the latest news articles for any Steam game.
     *
     * @see NewsResource
     */
    public function news(): NewsResource
    {
        return new NewsResource($this->apiKey);
    }

    /**
     * Access user-related API methods (ISteamUser).
     *
     * Fetch player profiles and friend lists.
     *
     * @see UserResource
     */
    public function users(): UserResource
    {
        return new UserResource($this->apiKey);
    }

    /**
     * Access player service API methods (IPlayerService).
     *
     * Fetch a player's owned games and recently played games.
     *
     * @see PlayerResource
     */
    public function player(): PlayerResource
    {
        return new PlayerResource($this->apiKey);
    }

    /**
     * Access user stats API methods (ISteamUserStats).
     *
     * Fetch achievements and stats for players or globally for a game.
     *
     * @see UserStatsResource
     */
    public function userStats(): UserStatsResource
    {
        return new UserStatsResource($this->apiKey);
    }

    public function apps(): AppResource
    {
        return new AppResource($this->apiKey);
    }

    public function economy()
    {
        return new EconomyResource($this->apiKey);
    }

    /**
     * Access WebAPI utility methods (ISteamWebAPIUtil).
     *
     * Fetch supported API lists and server info.
     *
     * @see WebAPIUtilResource
     */
    public function webAPIUtil(): WebAPIUtilResource
    {
        return new WebAPIUtilResource($this->apiKey);
    }

    /**
     * Access game server methods (ISteamGameServer).
     *
     * Fetch server status and SteamIDs by IP.
     *
     * @see GameServerResource
     */
    public function gameServer(): GameServerResource
    {
        return new GameServerResource($this->apiKey);
    }

    /**
     * Access leaderboard methods (ISteamLeaderboards).
     *
     * Fetch leaderboard definitions and entries.
     *
     * @see LeaderboardResource
     */
    public function leaderboards(): LeaderboardResource
    {
        return new LeaderboardResource($this->apiKey);
    }

    /**
     * Access matchmaking methods (ISteamMatchmaking).
     *
     * Fetch lobby lists and data.
     *
     * @see MatchmakingResource
     */
    public function matchmaking(): MatchmakingResource
    {
        return new MatchmakingResource($this->apiKey);
    }

    /**
     * Access published file methods (IPublishedFileService).
     *
     * Fetch Workshop file details and subscriptions.
     *
     * @see PublishedFileResource
     */
    public function publishedFile(): PublishedFileResource
    {
        return new PublishedFileResource($this->apiKey);
    }

    /**
     * Access user authentication methods (ISteamUserAuth).
     *
     * Authenticate users with Steam tickets.
     *
     * @see UserAuthResource
     */
    public function userAuth(): UserAuthResource
    {
        return new UserAuthResource($this->apiKey);
    }
}