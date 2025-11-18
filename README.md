# Bundle-demo-search-entreprise
## Recherche Entreprises Bundle

Bundle Symfony pour l'[API Recherche d'entreprises](https://recherche-entreprises.api.gouv.fr/) du gouvernement français.

### 📦 Installation

```bash
composer require aurelbichop/recherche-entreprises-bundle
```

### ⚙️ Configuration (optionnelle)

```yaml
# config/packages/yoanbernabeu_recherche_entreprises.yaml
aurel_bichop_recherche_entreprises:
    timeout: 10  # Timeout en secondes (défaut: 10)
```

### 🚀 Utilisation

#### Dans un Contrôleur

```php
use AurelBichop\RechercheEntreprisesBundle\Client\EntrepriseSearchClientInterface;

public function __construct(
    private EntrepriseSearchClientInterface $entrepriseClient
) {}

public function search(): Response
{
    $result = $this->entrepriseClient->search('carrefour');
    
    foreach ($result->results as $entreprise) {
        echo $entreprise->nomComplet . ' - ' . $entreprise->siren;
    }
}
```

#### Recherche par SIREN

```php
$entreprise = $this->entrepriseClient->findBySiren('652014051');

if ($entreprise) {
    echo $entreprise->nomComplet;
}
```

#### Commande Console

```bash
# Recherche simple
php bin/console recherche-entreprise:search carrefour

# Par SIREN (détails complets)
php bin/console recherche-entreprise:search 652014051 --siren
```

### 📊 Modèles

- `SearchResult` : Résultat paginé
- `Entreprise` : Données d'entreprise
- `Siege` : Établissement siège

### 🧪 Tests

```bash
vendor/bin/phpunit --testdox
```

### 📝 Licence

[MIT](LICENSE)