<?php

namespace AurelBichop\RechercheEntreprisesBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use AurelBichop\RechercheEntreprisesBundle\Client\EntrepriseSearchClientInterface;

#[AsCommand(
    name: 'recherche-entreprise:search',
    description: 'Recherche une entreprise via l\'API',
)]
class SearchEntrepriseCommand extends Command
{
    public function __construct(
        private readonly EntrepriseSearchClientInterface $entrepriseSearch,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('query', InputArgument::REQUIRED, 'Termes de recherche')
            ->addOption('siren', 's', InputOption::VALUE_NONE, 'Recherhce par SIREN')
            ->addOption('per-page', 'p', InputOption::VALUE_OPTIONAL, 'Nombre de résultats', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $query = $input->getArgument('query');
        $isSiren = $input->getOption('siren');
        $perPage = (int) $input->getOption('per-page');

        try {
            if ($isSiren) {
                $entreprise = $this->entrepriseSearch->findBySiren($query);

                if (!$entreprise) {
                    $io->warning('Aucune entreprise trouvée');

                    return Command::SUCCESS;
                }

                $io->success('Entreprise trouvée !');
                $io->definitionList(
                    ['SIREN' => $entreprise->siren],
                    ['Nom' => $entreprise->nomComplet],
                    ['Adresse' => $entreprise->siege?->adresse ?? 'N/A'],
                    ['Code NAF' => $entreprise->activitePrincipale ?? 'N/A'],
                    ['Actif' => $entreprise->isActif() ? '✅ Oui' : '❌ Non'],
                );
            } else {
                $result = $this->entrepriseSearch->search($query, 1, $perPage);

                if (!$result->hasResults()) {
                    $io->warning('Aucun résultat trouvé');

                    return Command::SUCCESS;
                }

                $io->success(sprintf('%d résultat(s) trouvé(s)', $result->totalResults));

                $rows = [];
                foreach ($result->results as $entreprise) {
                    $rows[] = [
                        $entreprise->siren,
                        substr($entreprise->nomComplet, 0, 40),
                        $entreprise->siege?->codePostal ?? 'N/A',
                        $entreprise->isActif() ? '✅ ' : '❌ ',
                    ];
                }

                $io->table(['SIREN', 'Nom', 'CP', 'Actif'], $rows);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur : '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
