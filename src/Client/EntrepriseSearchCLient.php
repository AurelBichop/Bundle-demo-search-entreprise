<?php

namespace AurelBichop\RechercheEntreprisesBundle\Client;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use AurelBichop\RechercheEntreprisesBundle\Model\Entreprise;
use AurelBichop\RechercheEntreprisesBundle\Model\SearchResult;

/**
 * Client pour interroger l'API Recherche d'entreprises.
 */
class EntrepriseSearchCLient implements EntrepriseSearchClientInterface
{
    private const string API_BASE_URL = 'https://recherche-entreprises.api.gouv.fr';
    private const int MAX_PER_PAGE = 25;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ?LoggerInterface $logger,
        private readonly int $timeout = 10,
    ) {
    }

    /**
     * Recherche des entreprises par nom, adresse, etc.
     */
    public function search(
        string $query,
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
    ): SearchResult {
        try {
            $params = array_merge([
                'q' => $query,
                'page' => max(1, $page),
                'per_page' => min($perPage, self::MAX_PER_PAGE),
            ], $filters);

            $response = $this->httpClient->request('GET', self::API_BASE_URL.'/search',
                [
                    'query' => $params,
                    'timeout' => $this->timeout,
                ]);

            $data = $response->toArray();

            $this->logger->info('Recherche entreprise effectuée', [
                'query' => $query,
                'total_results' => $data['total_results'] ?? 0,
            ]);

            return SearchResult::fromArray($data);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la recherche entreprise', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException(sprintf('Erreur lors de la recherche : %s', $e->getMessage()), 0, $e);
        }
    }

    public function findBySiren(string $siren): ?Entreprise
    {
        // Nettoyer le SIREN (enlever espaces, tirets)
        $siren = preg_replace('/[^0-9]/', '', $siren);

        if (9 !== strlen($siren)) {
            throw new \InvalidArgumentException('Le SIREN doit contenir exactement 9 chiffres');
        }

        $result = $this->search($siren, 1, 1);

        if (!$result->hasResults()) {
            return null;
        }

        $entreprise = $result->getFirstResult();
        // Vérifier que le SIREN correspond exactement
        if ($entreprise && $entreprise->siren === $siren) {
            return $entreprise;
        }

        return null;
    }

    public function searchByCodePostal(
        string $codePostal,
        int $page = 1,
        int $perPage = 10,
    ): SearchResult {
        return $this->search('', $page, $perPage, [
            'code_postal' => $codePostal,
        ]);
    }
}