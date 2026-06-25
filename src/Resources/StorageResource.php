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
class StorageResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Retrieve details for one or more Steam Workshop collections.
     *
     * Returns information about the contents of the specified Workshop
     * collections, including the published file IDs contained within them.
     *
     * @param  array $publishedFileIds List of Workshop collection IDs to query (uint64[])
     * @return object
     * @throws SteamApiException
     */
    public function GetCollectionDetails(array $publishedFileIds): object
    {
        $params = [
            'collectioncount' => count($publishedFileIds),
        ];

        foreach ($publishedFileIds as $index => $publishedFileId) {
            $params["publishedfileids[{$index}]"] = $publishedFileId;
        }

        return $this->post(
            'ISteamRemoteStorage',
            'GetCollectionDetails',
            SteamEndPoints::VERSION_1,
            $params
        );
    }

    /**
     * Retrieve details for one or more Steam Workshop published files.
     *
     * Returns metadata about the specified Workshop items, such as title,
     * description, creator, tags, preview images, statistics, and more.
     *
     * @param  array $publishedFileIds List of Workshop published file IDs to query (uint64[])
     * @return object
     * @throws SteamApiException
     */
    public function GetPublishedFileDetails(array $publishedFileIds): object
    {
        $params = [
            'itemcount' => count($publishedFileIds),
        ];

        foreach ($publishedFileIds as $index => $publishedFileId) {
            $params["publishedfileids[{$index}]"] = $publishedFileId;
        }

        return $this->post(
            'ISteamRemoteStorage',
            'GetPublishedFileDetails',
            'v1',
            $params
        );
    }

    /**
     * Retrieve details for a Steam UGC file.
     *
     * Returns information about a User Generated Content (UGC) file,
     * including its download URL, filename, size, and owner.
     *
     * @param  string      $ugcId    ID of the UGC file to retrieve (uint64)
     * @param  int         $appid    AppID of the associated game or application (uint32)
     * @param  string|null $steamId  Optional SteamID64 of the owner to validate against (uint64)
     * @return object
     * @throws SteamApiException
     */
    public function GetUGCFileDetails(
        string $ugcId,
        int $appid,
        ?string $steamId = null
    ): object
    {
        $params = [
            'ugcid'  => $ugcId,
            'appid'  => $appid,
        ];

        if ($steamId !== null) {
            $params['steamid'] = $steamId;
        }

        return $this->get(
            'ISteamRemoteStorage',
            'GetUGCFileDetails',
            'v1',
            $params
        );
    }
}