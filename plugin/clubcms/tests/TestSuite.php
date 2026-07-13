<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use Throwable;

final class TestSuite
{
    /**
     * @return array<int, array{name: string, status: string, message: string}>
     */
    public function run(?string $testClass = null): array
    {
        $results = [];

        foreach ($this->testClasses() as $className) {
            if ($testClass !== null && $className !== $testClass) {
                continue;
            }

            $test = new $className();

            try {
                $test->run();
                $results[] = [
                    'name' => $className,
                    'status' => 'passed',
                    'message' => '',
                ];
            } catch (Throwable $exception) {
                $results[] = [
                    'name' => $className,
                    'status' => 'failed',
                    'message' => $exception->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * @return array<int, array{class: class-string, label: string}>
     */
    public function availableTests(): array
    {
        return array_map(
            static fn (string $className): array => [
                'class' => $className,
                'label' => self::labelFor($className),
            ],
            $this->testClasses()
        );
    }

    public static function labelFor(string $className): string
    {
        $shortName = substr($className, strrpos($className, '\\') + 1);
        return $shortName;
    }

    /**
     * @return array<int, class-string>
     */
    private function testClasses(): array
    {
        return [
            FieldDefinitionTest::class,
            CategoryTest::class,
            CardTest::class,
            SettingsSubmissionHandlerTest::class,
            LandingPageRendererTest::class,
            LandingPageShortcodeTest::class,
            LandingPageColumnShortcodeTest::class,
        ];
    }
}
