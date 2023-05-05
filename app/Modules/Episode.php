<?php declare(strict_types=1);

namespace App\Modules;

class Episode
{
    private int $id;
    private string $name;
    private string $airDate;
    private string $episode;
    private array $characters;

    public function __construct(int $id, string $name, string $airDate, string $episode, array $characters)
    {
        $this->id = $id;
        $this->name = $name;
        $this->airDate = $airDate;
        $this->episode = $episode;
        $this->characters = $characters;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAirDate(): string
    {
        return $this->airDate;
    }

    public function getCharacters(): array
    {
        return $this->characters;
    }

    public function getEpisode(): string
    {
        return $this->episode;
    }

}