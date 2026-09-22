<?php

declare(strict_types=1);

function loadJsonArray(string $path): array
{
    if (! is_file($path)) {
        fwrite(STDERR, "Missing required file: {$path}\n");
        exit(1);
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    if (! is_array($decoded)) {
        fwrite(STDERR, "Invalid JSON in {$path}\n");
        exit(1);
    }

    return $decoded;
}

$versions = [];

if (getenv('INPUT_UPCOMINGRELEASES') == 'true') {
    $versions = array_merge($versions, loadJsonArray('/app/upcoming-releases.json'));
}

$versions = array_merge($versions, loadJsonArray('/app/supported-versions.json'));

echo 'Found the following supported versions: ', implode(', ', $versions), PHP_EOL;
file_put_contents(getenv('GITHUB_OUTPUT'), 'versions=' . json_encode($versions) . "\n", FILE_APPEND);
