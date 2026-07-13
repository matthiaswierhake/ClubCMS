<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/FieldDefinitionTest.php';
require_once __DIR__ . '/CategoryTest.php';

use ClubCMS\Tests\FieldDefinitionTest;
use ClubCMS\Tests\CategoryTest;

$tests = [
    FieldDefinitionTest::class,
    CategoryTest::class,
];

$failures = 0;

foreach ($tests as $testClass) {
    $test = new $testClass();

    try {
        $test->run();
        fwrite(STDOUT, '[OK] ' . $testClass . PHP_EOL);
    } catch (\Throwable $exception) {
        $failures++;
        fwrite(STDERR, '[FAIL] ' . $testClass . PHP_EOL);
        fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    }
}

if ($failures > 0) {
    exit(1);
}

fwrite(STDOUT, 'All tests passed.' . PHP_EOL);
