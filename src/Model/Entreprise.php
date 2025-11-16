<?php

declare(strict_types=1);

namespace AurelBichop\RechercheEntreprisesBundle\Model;

/**
 * Représente une entreprise de l'API Recherche d'entreprises.
 */
class Entreprise
{
    public function __construct(
        public readonly string $siren,
        public readonly string $nomComplet,
        public readonly ?string $nomRaisonSociale = null,
        public readonly ?Siege $siege = null,
        public readonly ?string $activitePrincipale = null,
        public readonly ?string $natureJuridique = null,
        public readonly ?string $dateCreation = null,
        public readonly ?string $etatAdministratif = null,
        public readonly ?int $nombreEtablissements = null,
        public readonly ?bool $estAssociation = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $siege = isset($data['siege']) ? Siege::fromArray($data['siege']) : null;

        return new self(
            siren: $data['siren'] ?? '',
            nomComplet: $data['nom_complet'] ?? '',
            nomRaisonSociale: $data['nom_raison_sociale'] ?? null,
            siege: $siege,
            activitePrincipale: $data['activite_principale'] ?? null,
            natureJuridique: $data['nature_juridique'] ?? null,
            dateCreation: $data['date_creation'] ?? null,
            etatAdministratif: $data['etat_administratif'] ?? null,
            nombreEtablissements: $data['nombre_etablissements'] ?? null,
            estAssociation: $data['est_association'] ?? null,
        );
    }

    public function isActif(): bool
    {
        return 'A' === $this->etatAdministratif;
    }

    public function getCodeNaf(): ?string
    {
        return $this->activitePrincipale;
    }
}
