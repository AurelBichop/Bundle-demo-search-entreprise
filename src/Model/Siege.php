<?php

declare(strict_types=1);

namespace AurelBichop\RechercheEntreprisesBundle\Model;
class Siege
{
    public function __construct(
        public readonly string $siret,
        public readonly string $adresse,
        public readonly ?string $codePostal = null,
        public readonly ?string $commune = null,
        public readonly ?string $etatAdministratif = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            siret: $data['siret'] ?? '',
            adresse: $data['adresse'] ?? '',
            codePostal: $data['codePostal'] ?? null,
            commune: $data['commune'] ?? null,
            etatAdministratif: $data['etatAdministratif'] ?? null,
        );
    }

    public function isActif(): bool
    {
        return 'A' === $this->etatAdministratif;
    }
}
