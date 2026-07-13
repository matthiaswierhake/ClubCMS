<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use Throwable;

final class TestSuite
{
    /**
     * @return array<int, array{name: string, status: string, message: string}>
     */
    public function run(): array
    {
        $results = [];

        foreach ($this->testClasses() as $testClass) {
            $test = new $testClass();

            try {
                $test->run();
                $results[] = [
                    'name' => $testClass,
                    'status' => 'passed',
                    'message' => '',
                ];
            } catch (Throwable $exception) {
                $results[] = [
                    'name' => $testClass,
                    'status' => 'failed',
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * @return array<int, class-string>
     */
    private function testClasses(): array
    {
        return [
            FieldDefinitionTest::class,
            CategoryTest::class,
        ];
    }
}
