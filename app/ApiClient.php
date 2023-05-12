<?php declare(strict_types=1);

namespace App;
require_once __DIR__ . '/Modules/Page.php';
require_once __DIR__ . '/Modules/Episode.php';
require_once __DIR__ . '/Modules/Location.php';
require_once __DIR__ . '/Modules/Character.php';

use App\Modules\Episode;
use App\Modules\Location;
use App\Modules\Page;
use GuzzleHttp\Client;
use App\Modules\Character;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;

class ApiClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://rickandmortyapi.com/api/'
        ]);
    }

    public function fetchCharacters(int $page): array
    {
        try {
            $cacheKey = 'characters_' . $page;
            if (!Cache::has($cacheKey)) {
                $response = $this->client->get('character/', [
                    'query' => [
                        'page' => $page,
                    ]
                ]);

                $responseContents = $response->getBody()->getContents();
                Cache::remember($cacheKey, $responseContents);
            } else {
                $responseContents = Cache::get($cacheKey);
            }

            $characters = json_decode($responseContents);
            $characterCollection = [];
            foreach ($characters->results as $character) {
                $characterCollection[] = $this->createCharacter($character);
            }
            $pageInfo = new Page($characters->info);
            return
                [
                    'characters' => $characterCollection,
                    'currentPage' => $page,
                    'page' => $pageInfo,
                ];
        } catch (GuzzleException $exception) {
            return [];
        }
    }

    public function fetchLocations(int $page): array
    {
        {
            try {
                $cacheKey = 'locations_' . $page;
                if (!Cache::has($cacheKey)) {
                    $response = $this->client->get('location/?page=' . $page);
                    $responseContents = $response->getBody()->getContents();
                    Cache::remember($cacheKey, $responseContents);
                } else {
                    $responseContents = Cache::get($cacheKey);
                }

                $locations = json_decode($responseContents);
                $locationCollection = [];
                foreach ($locations->results as $location) {
                    $locationCollection[] = $this->createLocation($location);
                }
                $pageInfo = new Page($locations->info);
                return [
                    'locations' => $locationCollection,
                    'page' => $pageInfo,
                    'currentPage' => $page
                ];
            } catch (GuzzleException $exception) {
                return [];
            }
        }
    }
    public function fetchSingleEpisode(int $id): ?Episode
    {
        try {
            $cacheKey = 'episode_' . $id;
            if (!Cache::has($cacheKey)) {
                $response = $this->client->get('episode/' . $id);
                $responseContents = $response->getBody()->getContents();
                Cache::remember($cacheKey, $responseContents);
            } else {
                $responseContents = Cache::get($cacheKey);
            }

            return $this->createEpisode(json_decode($responseContents));
        } catch (GuzzleException $exception) {
            return null;
        }
    }


    public function fetchSingleLocation(int $id): ?Location
    {
        if (!$id) {
            return null;
        }
        try {
            $cacheKey = 'location_' . $id;
            if (!Cache::has($cacheKey)) {
                $response = $this->client->get('location/' . $id);
                $responseContents = $response->getBody()->getContents();
                Cache::remember($cacheKey, $responseContents);
            } else {
                $responseContents = Cache::get($cacheKey);
            }
            return $this->createLocation(json_decode($responseContents));
        } catch (GuzzleException $exception) {
            return null;
        }
    }
    public function fetchSingleCharacter(int $id): ?Character
    {
        try {
            $cacheKey = 'character_' . $id;
            if (!Cache::has($cacheKey)) {
                $response = $this->client->get('character/' . $id);
                $responseContents = $response->getBody()->getContents();
                Cache::remember($cacheKey, $responseContents);
            } else {
                $responseContents = Cache::get($cacheKey);
            }

            return $this->createCharacter(json_decode($responseContents));

        } catch (GuzzleException $exception) {
            return null;
        }
    }
    public function fetchEpisodesById(array $ids): array
    {
        try {
            $episodes = [];
            $response = json_decode($this->client->get(
                'episode/' . implode(',', $ids))->getBody()->getContents());

            if (count($ids) === 1) {
                $episodes [] = $this->createEpisode($response);
            } else {
                foreach ($response as $episode) {
                    $episodes[] = $this->createEpisode($episode);
                }
            }
            return $episodes;
        } catch (GuzzleException $exception) {
            return [];
        }
    }
    public function searchCharacters(int $page, string $name, string $status, string $gender): array
    {
        try {
            $cacheKey = 'characters_' . $page . '_' . $name . '_' . $status . '_' . $gender;
            if (!Cache::has($cacheKey)) {
                $response = $this->client->get('character/', [
                    'query' => [
                        'page' => $page,
                        'name' => $name,
                        'status' => $status,
                        'gender' => $gender
                    ]
                ]);

                $responseContents = $response->getBody()->getContents();
                Cache::remember($cacheKey, $responseContents);
            } else {
                $responseContents = Cache::get($cacheKey);
            }

            $characters = json_decode($responseContents);
            $characterCollection = [];
            foreach ($characters->results as $character) {
                $characterCollection[] = $this->createCharacter($character);
            }
            $pageInfo = new Page($characters->info);
            return
                [
                    'characters' => $characterCollection,
                    'currentPage' => $page,
                    'page' => $pageInfo,
                    'name' => $name,
                    'gender' => $gender,
                    'status' => $status
                ];
        } catch (GuzzleException $exception) {
            return [];
        }
    }
    public function fetchEpisodeCount(): int
    {
        try {
            $response = json_decode($this->client->get('episode/')->getBody()->getContents());
            return $response->info->count;
        } catch (GuzzleException $exception) {
            return 0;
        }
    }
    public function fetchMultipleEpisodesById(array $ids): array
    {
        try {
            $episodes = [];
            $response = json_decode($this->client->get(
                'episode/' . implode(',', $ids))->getBody()->getContents());

            if (count($ids) === 1) {
                $episodes [] = $this->createEpisode($response);
            } else {
                foreach ($response as $episode) {
                    $episodes[] = $this->createEpisode($episode);
                }
            }
            return $episodes;
        } catch (GuzzleException $exception) {
            return [];
        }
    }
    public function fetchMultipleCharactersById(?array $id): array
    {
        if (!$id) {
            return [];
        }

        try {
            $response = json_decode($this->client->get(
                'character/' . implode(',', $id))->getBody()->getContents());
            $characters = [];
            if (count($id) === 1) {
                $characters [] = $this->fetchSingleCharacter($response->id);
            } else {
                foreach ($response as $character) {
                    $characters[] = $this->fetchSingleCharacter($character->id);
                }
            }
            return $characters;
        } catch (GuzzleException $exception) {
            return [];
        }
    }
    private function createCharacter(stdClass $character): Character
    {
        $episodeIds = [];
        foreach ($character->episode as $episode) {
            $episodeIds[] = basename($episode);
        }
        return new Character(
            $character->id,
            $character->name,
            $character->status,
            $character->species,
            $this->fetchSingleLocation((int)basename($character->origin->url)),
            $this->fetchSingleLocation((int)basename($character->location->url)),
            $character->image,
            $character->url,
            $episodeIds,
            $this->fetchSingleEpisode((int)$episodeIds[0])
        );
    }

    private function createEpisode(stdClass $episode): Episode
    {
        $characterIds = [];
        foreach ($episode->characters as $character) {
            $characterIds[] = basename($character);
        }
        return new Episode(
            $episode->id,
            $episode->name,
            $episode->air_date,
            (int)substr($episode->episode, 2, 2),
            (int)substr($episode->episode, 4, 5),
            $characterIds
        );
    }
    private function createLocation(stdClass $episode): Location
    {
        $characterIds = [];
        if (empty($episode->residents)) {
            $characterIds = null;
        } else {
            foreach ($episode->residents as $character) {
                $characterIds[] = basename($character);
            }
        }
        return new Location(
            $episode->id,
            $episode->name,
            $episode->type,
            $episode->dimension,
            $characterIds
        );
    }

}