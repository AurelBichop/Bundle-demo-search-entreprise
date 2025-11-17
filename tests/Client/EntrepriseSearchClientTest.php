<?php

namespace AurelBichop\RechercheEntreprisesBundle\Tests\Client;

use AurelBichop\RechercheEntreprisesBundle\Client\EntrepriseSearchCLient;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
class EntrepriseSearchClientTest extends TestCase
{

    public function testSearchReturnsResults(): void
    {
        $mockResponse = new MockResponse(json_encode([
            'results' => [
                [
                    'siren' => '123456789',
                    'nom_complet' => 'TEST ENTREPRISE',
                    'siege' => [
                        'siret' => '12345678900001',
                        'adresse' => '1 RUE DE TEST',
                    ],
                ],
            ],
            'total_results' => 1,
            'page' => 1,
            'per_page' => 10,
            'total_pages' => 1,
        ]));

        $httpClient = new MockHttpClient($mockResponse);
        $client = new EntrepriseSearchClient($httpClient, new NullLogger());

        $result = $client->search('test');

        $this->assertTrue($result->hasResults());
        $this->assertCount(1, $result->results);
        $this->assertEquals('123456789', $result->results[0]->siren);
    }
    public function testFindBySirenWithInvalidSiren(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $httpClient = new MockHttpClient();
        $client = new EntrepriseSearchClient($httpClient, new NullLogger());

        $client->findBySiren('12345'); // SIREN invalide
    }
}