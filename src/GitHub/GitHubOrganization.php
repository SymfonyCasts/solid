<?php

namespace App\GitHub;

class GitHubOrganization
{
    private $name;

    private $repositoryCount;

    public function __construct(string $organizationName, int $repositoryCount)
    {
        $this->name = $organizationName;
        $this->repositoryCount = $repositoryCount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRepositoryCount(): int
    {
        return $this->repositoryCount;
    }
}
