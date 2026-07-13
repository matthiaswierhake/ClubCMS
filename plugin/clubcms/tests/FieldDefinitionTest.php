<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\FieldDefinition;
use RuntimeException;

final class FieldDefinitionTest
{
    public function run(): void
    {
        $this->itCreatesAnArrayRepresentation();
        $this->itNormalizesFieldKeysInArrayRepresentation();
        $this->itBuildsFromArray();
        $this->itFallsBackToDefaultsWhenDataIsMissing();
    }

    private function itCreatesAnArrayRepresentation(): void
    {
        $definition = new FieldDefinition(
            'fd-news',
            'News',
            [
                ['name' => 'headline', 'type' => 'text'],
                ['name' => 'teaser', 'type' => 'textarea'],
            ]
        );

        $this->assertSame(
            [
                'id' => 'fd-news',
                'label' => 'News',
                'fields' => [
                    ['name' => 'headline', 'type' => 'text'],
                    ['name' => 'teaser', 'type' => 'textarea'],
                ],
            ],
            $definition->toArray(),
            'toArray() should expose the stored data.'
        );
    }

    private function itNormalizesFieldKeysInArrayRepresentation(): void
    {
        $definition = new FieldDefinition(
            'fd-events',
            'Events',
            [
                2 => ['name' => 'starts_at', 'type' => 'datetime'],
                7 => ['name' => 'location', 'type' => 'text'],
            ]
        );

        $this->assertSame(
            [
                'id' => 'fd-events',
                'label' => 'Events',
                'fields' => [
                    ['name' => 'starts_at', 'type' => 'datetime'],
                    ['name' => 'location', 'type' => 'text'],
                ],
            ],
            $definition->toArray(),
            'toArray() should reindex the fields array.'
        );
    }

    private function itBuildsFromArray(): void
    {
        $definition = FieldDefinition::fromArray([
            'id' => 'fd-members',
            'label' => 'Members',
            'fields' => [
                4 => ['name' => 'member_name', 'type' => 'text'],
                9 => ['name' => 'member_since', 'type' => 'date'],
            ],
        ]);

        $this->assertSame('fd-members', $definition->id, 'fromArray() should map id.');
        $this->assertSame('Members', $definition->label, 'fromArray() should map label.');
        $this->assertSame(
            [
                ['name' => 'member_name', 'type' => 'text'],
                ['name' => 'member_since', 'type' => 'date'],
            ],
            $definition->fields,
            'fromArray() should normalize fields.'
        );
    }

    private function itFallsBackToDefaultsWhenDataIsMissing(): void
    {
        $definition = FieldDefinition::fromArray([]);

        $this->assertSame('', $definition->id, 'Missing id should default to empty string.');
        $this->assertSame('', $definition->label, 'Missing label should default to empty string.');
        $this->assertSame([], $definition->fields, 'Missing fields should default to an empty array.');
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    private function assertSame(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true));
        }
    }
}
