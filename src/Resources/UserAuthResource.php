<?php

namespace PmZedx\SteamService\Resources;

use PmZedx\SteamService\Concerns\MakesHttpRequests;
use PmZedx\SteamService\Endpoints\SteamEndPoints;
use PmZedx\SteamService\Exceptions\SteamApiException;

/**
 * Handles the ISteamUserAuth API interface.
 *
 * Provides methods for authenticating users with Steam tickets.
 */
class UserAuthResource
{
    use MakesHttpRequests;

    public function __construct(protected string $apiKey)
    {
        $this->httpClient = $this->buildHttpClient();
    }

    /**
     * Authenticate a user using a Steam authentication ticket.
     *
     * @param string $steamId    The 64-bit Steam ID of the user.
     * @param string $ticket     The authentication ticket (from Steam client).
     * @param string $sessionKey Optional session key (base64 encoded).
     * @param string $encrypted  Optional encrypted data.
     *
     * @return object Authentication result.
     *
     * @throws SteamApiException
     *
     * @example
     *   $auth = $steam->userAuth()->authenticateUser('76561197960435530', 'ticket_here');
     */
    public function authenticateUser(string $steamId, string $ticket, ?string $sessionKey = null, ?string $encrypted = null): object
    {
        $params = [
            'steamid' => $steamId,
            'ticket'  => $ticket,
        ];

        if ($sessionKey !== null) {
            $params['sessionkey'] = $sessionKey;
        }

        if ($encrypted !== null) {
            $params['encrypted'] = $encrypted;
        }

        return $this->post('ISteamUserAuth', 'AuthenticateUser', SteamEndPoints::VERSION_1, $params);
    }
}