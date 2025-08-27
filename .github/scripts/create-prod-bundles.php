<?php
// Create production bundles config (exclude dev bundles)
// This script should be run from the service directory (backend/ or audit-service/)

// Detect which service we're in based on current directory
$currentDir = basename(getcwd());
echo "Detected service: $currentDir\n";

if ($currentDir === 'backend') {
    $bundles = [
        Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
        Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
        Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
        Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
        Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
        Nelmio\CorsBundle\NelmioCorsBundle::class => ['all' => true],
        ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
        Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
        Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    ];
} elseif ($currentDir === 'audit-service') {
    $bundles = [
        Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
        Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
        Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
        Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
        Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
        Nelmio\CorsBundle\NelmioCorsBundle::class => ['all' => true],
        ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
        Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
        Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    ];
} else {
    echo "Error: Unknown service directory: $currentDir\n";
    exit(1);
}

$content = "<?php\n\nreturn " . var_export($bundles, true) . ";\n";
file_put_contents('config/bundles_prod.php', $content);
echo "Production bundles config created successfully for $currentDir\n";
