<?php

declare(strict_types=1);

$versions = [];

if (getenv('INPUT_UPCOMINGRELEASES') == 'true') {
    $versions[] = '8.3.0RC3';
}

$d = new DOMDocument();
@$d->loadHTML(file_get_contents('https://php.net/supported-versions')); // the variable $ads contains the HTML code above

foreach ((new DOMXPath($d))->query('//a') as $link) {
    $url = $link->getAttribute('href');

    if (strpos($url, '/downloads.php#v') === 0) {
        $versions[] = substr(
            $url,
            16,
            3
        );
    }
}

echo 'Found the following supported versions: ', implode(', ', $versions), PHP_EOL;
file_put_contents(getenv('GITHUB_OUTPUT'), 'versions=' . json_encode($versions) . "\n", FILE_APPEND);
