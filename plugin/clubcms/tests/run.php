<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/TestSuite.php';

use ClubCMS\Tests\TestSuite;

$failures = 0;

foreach ((new TestSuite())->run() as $result) {
    if ($result['status'] === 'passed') {
        fwrite(STDOUT, '[OK] ' . $result['name'] . PHP_EOL);
        continue;
    }

    fwrite(STDERR, '[FAIL] ' . $result['name'] . PHP_EOL);
    fwrite(STDERR, $result['message'] . PHP_EOL);
    $failures++;
}

if ($failures > 0) {
    exit(1);
}

fwrite(STDOUT, 'All tests passed.' . PHP_EOL);
