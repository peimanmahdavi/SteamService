<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamNews API interface.
 *
 * Lets you fetch the latest news entries for any Steam game.
 */
class NewsResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Get the latest news articles for a specific game.
     *
     * @param  int       $appId      The Steam AppID of the game (e.g. 440 for TF2)
     * @param  int|null  $count      How many news articles to return (default: 10)
     * @param  int|null  $maxLength  Max character length per article excerpt (0 = full length)
     *
     * @return array{appid: int, newsitems: array}
     *
     * @throws SteamApiException
     *
     * @example
     *   $news = $steam->news()->getForGame(440, count: 5);
     *   foreach ($news['appnews']['newsitems'] as $article) {
     *       echo $article['title'];
     *   }
     */
    public function getForGame(int $appId, ?int $count = 10, ?int $maxLength = 0): array
    {
        return $this->get('ISteamNews', 'GetNewsForApp', 2, [
            'appid'     => $appId,
            'count'     => $count,
            'maxlength' => $maxLength,
        ]);
    }
}