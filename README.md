# Steam API PHP

A clean, well-documented PHP wrapper for the Steam Web API built on top of Guzzle HTTP.

## Installation

```bash
composer require pmzedx/steam-service
```

## Getting an API Key

Request a Steam Web API key at: https://steamcommunity.com/dev/apikey

## Setup

### Option 1 — Environment variable (recommended)

Set `STEAM_API_KEY` in your `.env` or server environment:

```
STEAM_API_KEY=your_key_here
```

Then create the client with no arguments:

```php
use PmZedx\SteamService\SteamClient;

$steam = new SteamClient();
```

### Option 2 — Pass the key directly

```php
$steam = new SteamClient('your_api_key_here');
```

---

## Usage

### 👤 User Profiles — `$steam->users()`

```php
// Single profile
$data = $steam->users()->getProfiles('76561197960435530');
$player = $data['response']['players'][0];
echo $player['personaname']; // Display name
echo $player['avatarfull'];  // Avatar URL

// Multiple profiles (up to 100)
$data = $steam->users()->getProfiles([
    '76561197960435530',
    '76561197960287930',
]);

// Friend list
$data = $steam->users()->getFriends('76561197960435530');
foreach ($data['friendslist']['friends'] as $friend) {
    echo $friend['steamid'];
}
```

---

### 🎮 Player Games — `$steam->player()`

```php
// All owned games
$data = $steam->player()->getOwnedGames('76561197960435530');
echo $data['response']['game_count'];

// With options
$data = $steam->player()->getOwnedGames(
    steamId: '76561197960435530',
    includeGameDetails: true,
    includeFreeGames: true,
    filterToAppIds: [440, 730] // Only TF2 and CS2
);

// Recently played (last 2 weeks)
$data = $steam->player()->getRecentlyPlayedGames('76561197960435530', limit: 5);
foreach ($data['response']['games'] as $game) {
    echo $game['name'] . ': ' . $game['playtime_2weeks'] . ' minutes';
}
```

---

### 🏆 Achievements & Stats — `$steam->userStats()`

```php
// Global achievement unlock rates for a game (no auth needed)
$data = $steam->userStats()->getGlobalAchievementPercentages(440);
foreach ($data['achievementpercentages']['achievements'] as $a) {
    echo $a['name'] . ': ' . $a['percent'] . '%';
}

// A player's achievement status in a game
$data = $steam->userStats()->getPlayerAchievements('76561197960435530', 440, language: 'en');
foreach ($data['playerstats']['achievements'] as $a) {
    echo $a['name'] . ': ' . ($a['achieved'] ? '✅' : '❌');
}

// A player's in-game stats
$data = $steam->userStats()->getPlayerStats('76561197960435530', 440);
foreach ($data['playerstats']['stats'] as $stat) {
    echo $stat['name'] . ': ' . $stat['value'];
}
```

---

### 📰 Game News — `$steam->news()`

```php
$data = $steam->news()->getForGame(appId: 440, count: 5, maxLength: 300);
foreach ($data['appnews']['newsitems'] as $article) {
    echo $article['title'] . "\n" . $article['url'];
}
```

---

### 🛠️ WebAPI Utilities — `$steam->webAPIUtil()`

```php
// Get a list of all supported API interfaces and methods
$data = $steam->webAPIUtil()->getSupportedAPIList();
foreach ($data['apilist']['interfaces'] as $interface) {
    echo $interface['name'] . "\n";
}

// Get server info (current time, etc.)
$data = $steam->webAPIUtil()->getServerInfo();
echo $data['servertime'];
```

---

### 🖥️ Game Servers — `$steam->gameServer()`

```php
// Get SteamIDs associated with a given IP address
$data = $steam->gameServer()->getServerSteamIDsByIP('192.168.1.100');

// Get the current status of all game servers
$data = $steam->gameServer()->getGameServersStatus();
```

---

### 🏅 Leaderboards — `$steam->leaderboards()`

```php
// Get definitions of all leaderboards for a game
$data = $steam->leaderboards()->getLeaderboardDefinitions(440);

// Get entries from a specific leaderboard
$data = $steam->leaderboards()->getLeaderboardEntries(
    appId: 440,
    leaderboardId: 12345,
    start: 1,
    end: 10,
    steamId: '76561197960435530' // optional, to filter for a specific user
);
```

---

### 🔍 Matchmaking (Lobbies) — `$steam->matchmaking()`

```php
// Get a list of lobbies for a game
$data = $steam->matchmaking()->getLobbyList(
    appId: 440,
    max: 10,
    filter: 'gamedir:tf' // optional filter
);

// Get data for a specific lobby
$data = $steam->matchmaking()->getLobbyData('123456789012345678');
```

---

### 📦 Workshop (Published Files) — `$steam->publishedFile()`

```php
// Get details for one or more Workshop files
$data = $steam->publishedFile()->getDetails([123456, 789012]);

// Get subscriptions for a specific user
$data = $steam->publishedFile()->getSubscriptions('76561197960435530');
```

---

### 🔐 User Authentication — `$steam->userAuth()`

```php
// Authenticate a user using a Steam authentication ticket
$data = $steam->userAuth()->authenticateUser(
    steamId: '76561197960435530',
    ticket: 'your_ticket_here',
    sessionKey: 'optional_session_key', // base64 encoded
    encrypted: 'optional_encrypted_data'
);
```

---

## Error Handling

All methods throw `SteamApiException` on failure (bad key, rate limit, network error, etc.):

```php
use PmZedx\SteamService\Exceptions\SteamApiException;

try {
    $data = $steam->users()->getProfiles('76561197960435530');
} catch (SteamApiException $e) {
    echo 'Steam API error: ' . $e->getMessage();
}
```

---

## Package Structure

```
src/
├── SteamClient.php              ← Main entry point
├── Concerns/
│   └── MakesHttpRequests.php    ← Guzzle HTTP trait (shared by all resources)
├── Exceptions/
│   └── SteamApiException.php    ← Thrown on all API/network errors
└── Resources/
    ├── NewsResource.php         ← ISteamNews
    ├── UserResource.php         ← ISteamUser
    ├── PlayerResource.php       ← IPlayerService
    ├── UserStatsResource.php    ← ISteamUserStats
    ├── AppResource.php          ← ISteamApps
    ├── EconomyResource.php      ← ISteamEconomy
    ├── StorageResource.php      ← ISteamRemoteStorage
    ├── WebAPIUtilResource.php   ← ISteamWebAPIUtil
    ├── GameServerResource.php   ← ISteamGameServer
    ├── LeaderboardResource.php  ← ISteamLeaderboards
    ├── MatchmakingResource.php  ← ISteamMatchmaking
    ├── PublishedFileResource.php ← IPublishedFileService
    └── UserAuthResource.php     ← ISteamUserAuth
```

## License

MIT