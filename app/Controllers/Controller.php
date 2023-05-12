<?php declare(strict_types=1);

namespace App\Controllers;
require_once __DIR__. '/../ApiClient.php';
require_once __DIR__. '/../Core/View.php';
use App\ApiClient;
use App\Core\View;


class Controller
{
    private ApiClient $client;

    public function __construct()
    {
        $this->client = new ApiClient();
    }

    public function characters(array $vars): View
    {
        $page = $vars['page'] ?? 1;
        $name = $vars['name'] ?? $_GET['name'] ?? '';
        $response = $this->client->fetchCharacters($page);
        return new View('characters', $response);
    }

    public function singleCharacter(array $vars): View
    {
        $page = isset($vars['page']) ? (int)$vars['page'] : 1;
        $character = $this->client->fetchSingleCharacter($page);
        if (!$character) {
            return new View('notFound', []);
        }
        $episodes = $this->client->fetchEpisodesById($character->getEpisodeIds());
        return new View('singleCharacter', ['character' => $character, 'episodes' => $episodes]);
    }
    public function search(): View
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $name = $_GET['name'] ?? '';
        $status =  $_GET['status'] ?? '';
        $gender =  $_GET['gender'] ?? '';

        $characters = $this->client->searchCharacters($page, $name, $status, $gender);
        if (!$characters) {
            return new View('notFound', []);
        }
        else
            return new View('characters', $characters);
    }

    public function episodes(array $vars): View
    {
        $episodeCount = $this->client->fetchEpisodeCount();
        $response = $this->client->fetchMultipleEpisodesById(range(1,$episodeCount));
        return new View('episodes', ['episodes' => $response]);
    }

    public function locations(array $vars): View
    {
        $page = isset($vars['page']) ? (int)$vars['page'] : 1;
        $response = $this->client->fetchLocations($page);
        return new View('locations', $response);
    }
    public function singleEpisode(array $vars): View
    {
        $page = isset($vars['page']) ? (int)$vars['page'] : 1;
        $episode = $this->client->fetchSingleEpisode($page);
        if (!$episode) {
            return new View('notFound', []);
        }
        $characters = $this->client->fetchMultipleCharactersById($episode->getCharacterIds());
        return new View('singleEpisode', ['episode' => $episode, 'characters' => $characters]);
    }
    public function singleLocation(array $vars): View
    {
        $page = isset($vars['page']) ? (int)$vars['page'] : 1;
        $location = $this->client->fetchSingleLocation($page);
        if (!$location) {
            return new View('notFound', []);
        }
        $characters = $this->client->fetchMultipleCharactersById($location->getCharacterIds());
        return new View('singleLocation', ['location' => $location, 'characters' => $characters]);
    }
}