<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

class EconomyResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Retrieve asset class information for one or more item classes.
     *
     * Steam returns metadata about item classes such as names,
     * descriptions, tags, icons, and other economy-related data.
     *
     * @param  int         $appid        AppID of the game to query (uint32)
     * @param  array       $classIds     List of class IDs to retrieve information for (uint64[])
     * @param  array|null  $instanceIds  Optional list of instance IDs corresponding to each class ID (uint64[])
     * @param  string|null $language     ISO 639-1 language code for localized strings (e.g. "en", "de", "fr")
     * @return object
     * @throws SteamApiException
     */
    public function GetAssetClassInfo(
        int $appid,
        array $classIds,
        ?array $instanceIds = null,
        ?string $language = null
    ): object
    {
        $params = [
            'appid' => $appid,
            'class_count' => count($classIds),
        ];

        if ($language !== null) {
            $params['language'] = $language;
        }

        foreach ($classIds as $index => $classId) {
            $n = $index + 1;
            $params["classid{$n}"] = $classId;

            if ($instanceIds !== null && isset($instanceIds[$index])) {
                $params["instanceid{$n}"] = $instanceIds[$index];
            }
        }

        return $this->get(
            'ISteamEconomy',
            'GetAssetClassInfo',
            SteamEndPoints::VERSION_1,
            $params
        );
    }

    /**
     * Retrieve asset price information for a game's economy items.
     *
     * Returns the current price overview for assets in the specified game.
     * Prices can optionally be localized and filtered by currency.
     *
     * @param  int         $appid     AppID of the game to query (uint32)
     * @param  string|null $language  ISO 639-1 language code for localized strings (e.g. "en", "de", "fr")
     * @param  string|null $currency  ISO 4217 currency code for price filtering (e.g. "USD", "EUR", "GBP")
     * @return object
     * @throws SteamApiException
     */
    public function GetAssetPrices(
        int $appid,
        ?string $language = null,
        ?string $currency = null
    ): object
    {
        $params = [
            'appid' => $appid,
        ];

        if ($language !== null) {
            $params['language'] = $language;
        }

        if ($currency !== null) {
            $params['currency'] = $currency;
        }

        return $this->get(
            'ISteamEconomy',
            'GetAssetPrices',
            SteamEndPoints::VERSION_1,
            $params
        );
    }
}