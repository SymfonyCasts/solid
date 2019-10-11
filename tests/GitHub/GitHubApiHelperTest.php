<?php

namespace App\Tests\GotHub;

use App\GitHub\GitHubApiHelper;
use App\GitHub\GitHubOrganization;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class GitHubApiHelperTest extends TestCase
{
    public function testGetOrganizationInfoIntegration()
    {
        $httpClient = HttpClient::create();
        $apiHelper = new GitHubApiHelper($httpClient);
        $orgInfo = $apiHelper->getOrganizationInfo('SymfonyCasts');
        $this->assertInstanceOf(GitHubOrganization::class, $orgInfo);
        $this->assertGreaterThan(0, $orgInfo->getRepositoryCount());
    }
}
