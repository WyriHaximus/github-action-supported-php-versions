# Supported PHP Versions Github Action

Outputs the currently supported PHP versions as published on [php.net](https://www.php.net/supported-versions). The list is baked into the Docker image as [`supported-versions.json`](supported-versions.json) (no network call when the action runs).

## Maintaining version data

- **Supported versions:** Run `php bin/update-supported-versions.php` to refresh [`supported-versions.json`](supported-versions.json) from php.net. CI runs the same script with `--check` on every push and pull request. A [weekly workflow](.github/workflows/update-supported-versions.yml) opens a pull request when php.net changes.
- **Upcoming releases:** Edit [`upcoming-releases.json`](upcoming-releases.json) when you want `upcomingReleases: true` to include pre-release versions before they appear on php.net.

## Output

This action has only one output, namely `versions`, containing a JSON array with the supported versions.

## License ##

Copyright 2025 [Cees-Jan Kiewiet](http://wyrihaximus.net/)

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
