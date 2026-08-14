<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the IPublishedFileService API interface.
 *
 * Provides methods for fetching Workshop file details and subscriptions.
 */
class PublishedFileResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get details for one or more published files.
     *
     * @param int[] $fileIds Array of published file IDs.
     *
     * @return object File details.
     *
     * @throws SteamApiException
     *
     * @example
     *   $details = $steam->publishedFile()->getDetails([123456, 789012]);
     */
    public function getDetails(array $fileIds): object
    {
        return $this->get('IPublishedFileService', 'GetDetails', SteamEndPoints::VERSION_1, [
            'publishedfileids' => json_encode($fileIds),
        ]);
    }

    /**
     * Get subscriptions for a specific user.
     *
     * @param string $steamId The 64-bit Steam ID of the user.
     *
     * @return object Subscription list.
     *
     * @throws SteamApiException
     *
     * @example
     *   $subs = $steam->publishedFile()->getSubscriptions('76561197960435530');
     */
    public function getSubscriptions(string $steamId): object
    {
        return $this->get('IPublishedFileService', 'GetSubscriptions', SteamEndPoints::VERSION_1, [
            'steamid' => $steamId,
        ]);
    }
}