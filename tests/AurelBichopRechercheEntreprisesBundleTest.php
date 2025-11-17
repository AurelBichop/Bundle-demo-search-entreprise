<?php

namespace AurelBichop\RechercheEntreprisesBundle\Tests;

use AurelBichop\RechercheEntreprisesBundle\AurelBichopRechercheEntreprisesBundle;
use PHPUnit\Framework\TestCase;
class AurelBichopRechercheEntreprisesBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new AurelBichopRechercheEntreprisesBundle();
        $this->assertInstanceOf(AurelBichopRechercheEntreprisesBundle::class, $bundle);
    }

    public function testGetPath(): void
    {
        $bundle = new AurelBichopRechercheEntreprisesBundle();
        $path = $bundle->getPath();
        $this->assertDirectoryExists($path);
    }
}
