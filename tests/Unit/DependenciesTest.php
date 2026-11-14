<?php

declare(strict_types=1);

namespace Tests\Unit;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;

final class DependenciesTest extends TestCase
{
    /**
     * phpdotenv < 5.6.1 usa "string $fileEncoding = null" (nullable implícito),
     * que gera E_DEPRECATED no PHP 8.4. A correção (?string) veio na 5.6.1.
     */
    public function testPhpdotenvVersionHasExplicitNullableFix(): void
    {
        $version = InstalledVersions::getVersion('vlucas/phpdotenv');

        $this->assertNotNull($version);
        $this->assertTrue(
            version_compare($version, '5.6.1', '>='),
            "vlucas/phpdotenv {$version} gera deprecation de nullable implícito no PHP 8.4; use >= 5.6.1"
        );
    }
}
