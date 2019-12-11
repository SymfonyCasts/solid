<?php

namespace App\GitHub;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GitHubApiHelper
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getOrganizationInfo(string $organization): GitHubOrganization
    {
        $response = $this->httpClient->request('GET', 'https://api.github.com/orgs/'.$organization);

        $data = $response->toArray();

        return new GitHubOrganization(
            $data['name'],
            $data['public_repos']
        );
    }

    /**
     * @return GitHubRepository[]
     */
    public function getOrganizationRepositories(string $organization): array
   {
       $response = $this->httpClient->request('GET', sprintf('https://api.github.com/orgs/%s/repos', $organization));

       $data = $response->toArray();

       $repositories = [];
       foreach ($data as $repoData) {
           $repositories[] = new GitHubRepository(
               $repoData['name'],
               $repoData['html_url'],
               \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $repoData['updated_at'])
           );
       }

       return $repositories;
   }
}
