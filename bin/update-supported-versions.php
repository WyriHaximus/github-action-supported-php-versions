<?php

declare(strict_types=1);

function fetchSupportedVersions(): array
{
    $html = file_get_contents('https://php.net/supported-versions');
    if ($html === false) {
        fwrite(STDERR, "Failed to fetch https://php.net/supported-versions\n");
        exit(1);
    }

    $document = new DOMDocument();
    @$document->loadHTML($html);

    $versions = [];
    foreach ((new DOMXPath($document))->query('//a') as $link) {
        $url = $link->getAttribute('href');
        if (! str_starts_with($url, '/downloads.php?version=')) {
            continue;
        }

        $version = substr($url, strlen('/downloads.php?version='));
        if (preg_match('/^\d+\.\d+/', $version, $matches) !== 1) {
            continue;
        }

        $versions[] = $matches[0];
    }

    if ($versions === []) {
        fwrite(STDERR, "No supported versions found in php.net response\n");
        exit(1);
    }

    return $versions;
}

function loadCommittedVersions(string $outputPath): array
{
    if (! is_file($outputPath)) {
        fwrite(STDERR, "Missing {$outputPath}; run without --check to generate it\n");
        exit(1);
    }

    $decoded = json_decode((string) file_get_contents($outputPath), true);
    if (! is_array($decoded)) {
        fwrite(STDERR, "Invalid JSON in {$outputPath}\n");
        exit(1);
    }

    return $decoded;
}

function displayWidth(string $value): int
{
    if (function_exists('mb_strwidth')) {
        return mb_strwidth($value, 'UTF-8');
    }

    return strlen($value);
}

function padDisplay(string $value, int $width): string
{
    $padding = $width - displayWidth($value);
    if ($padding < 0) {
        return $value;
    }

    return $value . str_repeat(' ', $padding);
}

function formatTableRow(string $version, string $here, string $phpNet, string $match, int $versionWidth): string
{
    return sprintf(
        '| %s | %s | %s | %s |',
        padDisplay($version, $versionWidth),
        padDisplay($here, 4),
        padDisplay($phpNet, 7),
        padDisplay($match, 5),
    );
}

function printVersionCheckTable(array $committed, array $fetched): void
{
    $ordered = $fetched;
    foreach ($committed as $version) {
        if (! in_array($version, $ordered, true)) {
            $ordered[] = $version;
        }
    }

    $versionWidth = max(displayWidth('Version'), ...array_map(displayWidth(...), $ordered));

    echo formatTableRow('Version', 'Here', 'PHP.net', 'Match', $versionWidth), PHP_EOL;
    echo sprintf(
        '|%s|%s|%s|%s|',
        str_repeat('-', $versionWidth + 2),
        str_repeat('-', 6),
        str_repeat('-', 9),
        str_repeat('-', 7),
    ), PHP_EOL;

    foreach ($ordered as $version) {
        $inCommitted = in_array($version, $committed, true);
        $inFetched = in_array($version, $fetched, true);
        $here = $inCommitted ? '✅' : '❌';
        $php = $inFetched ? '✅' : '❌';
        $match = $inCommitted === $inFetched ? '✅' : '❌';

        echo formatTableRow($version, $here, $php, $match, $versionWidth), PHP_EOL;
    }
}

$checkOnly = in_array('--check', $argv, true);
$positionalArgs = array_values(array_filter(
    $argv,
    static fn (string $arg): bool => $arg !== '--check',
));
$outputPath = $positionalArgs[1] ?? 'supported-versions.json';

$versions = fetchSupportedVersions();
$encoded = json_encode($versions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

if ($checkOnly) {
    $committed = loadCommittedVersions($outputPath);

    echo "Comparing {$outputPath} with https://php.net/supported-versions", PHP_EOL, PHP_EOL;
    printVersionCheckTable($committed, $versions);

    if ($committed !== $versions) {
        echo PHP_EOL, "{$outputPath} is out of date with php.net; run bin/update-supported-versions.php", PHP_EOL;
        exit(1);
    }

    echo PHP_EOL, 'All versions match.', PHP_EOL;
    exit(0);
}

if (file_put_contents($outputPath, $encoded) === false) {
    fwrite(STDERR, "Failed to write {$outputPath}\n");
    exit(1);
}

echo 'Wrote supported versions: ', implode(', ', $versions), PHP_EOL;
