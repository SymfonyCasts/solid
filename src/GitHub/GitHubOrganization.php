<?php

namespace App\GitHub;

class GitHubOrganization
{
    private $name;

    private $description;

    private $repositoryCount;

    public function __construct(string $organizationName, string $description, int $repositoryCount)
    {
        $this->name = $organizationName;
        $this->description = $description;
        $this->repositoryCount = $repositoryCount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRepositoryCount(): int
    {
        return $this->repositoryCount;
    }
}
