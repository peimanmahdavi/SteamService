<?php

namespace PmZedx\SteamService\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

trait MakesHttpRequests
{
    protected Client $httpClient;

    protected string $baseUrl = SteamEndPoints::BASE_URL;

    /**
     * Send a GET request to the Steam Web API.
     *
     * @param string $interface The Steam API interface (e.g. "ISteamUser")
     * @param string $method The method to call (e.g. "GetPlayerSummaries")
     * @param string $version The method version (e.g. 2)
     * @param array $params Query parameters to include in the request
     *
     * @return object
     */
    protected function get(string $interface, string $method, string $version, array $params = []): object
    {
        $url = "{$this->baseUrl}/{$interface}/{$method}/{$version}/";

        $params = array_merge(['key' => $this->apiKey, 'format' => 'json'], $params);

        try {
            $response = $this->httpClient->get($url, ['query' => $params]);
            $body     = (string) $response->getBody();
            $decoded  = json_decode($body);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new SteamApiException('Steam API returned invalid JSON: ' . json_last_error_msg());
            }

            return $decoded;
        } catch (GuzzleException $e) {
            throw new SteamApiException(
                "Steam API request failed for {$interface}/{$method}: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Send a POST request to the Steam Web API.
     *
     * @param string $interface The Steam API interface (e.g. "ISteamUser")
     * @param string $method The method to call (e.g. "GetPlayerSummaries")
     * @param string $version The method version (e.g. 2)
     * @param array $params Query parameters to include in the request
     *
     * @return object
     */
    protected function post(string $interface, string $method, string $version, array $params = []): object
    {
        $url = "{$this->baseUrl}/{$interface}/{$method}/{$version}/";

        $params = array_merge([
            'key'    => $this->apiKey,
            'format' => 'json',
        ], $params);

        try {
            $response = $this->httpClient->post($url, [
                'form_params' => $params,
            ]);

            $decoded = json_decode(
                (string) $response->getBody()
            );

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new SteamApiException(
                    'Steam API returned invalid JSON: ' . json_last_error_msg()
                );
            }

            return $decoded;
        } catch (GuzzleException $e) {
            throw new SteamApiException(
                "Steam API request failed for {$interface}/{$method}: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Build and return a Guzzle HTTP client instance.
     */
    protected function buildHttpClient(): Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10.0,
            'headers'  => [
                'Accept'     => 'application/json',
                'User-Agent' => 'SteamApiPhp/1.0',
            ],
        ]);
    }
}