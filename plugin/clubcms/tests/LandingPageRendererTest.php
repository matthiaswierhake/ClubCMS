<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\Category;
use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use DateTimeImmutable;
use DateTimeZone;
use ClubCMS\Rendering\LandingPageRenderer;
use RuntimeException;

final class LandingPageRendererTest
{
    public function run(): void
    {
        $this->itRendersAFourColumnLandingPage();
        $this->itAddsEditorControlsForLoggedInUsers();
    }

    private function itRendersAFourColumnLandingPage(): void
    {
        $renderer = new LandingPageRenderer();
        $html = $renderer->render([
            new Category('cat-news', 'News', 'news', 'date'),
            new Category('cat-events', 'Termine', 'termine', 'manual'),
        ], [
            new Card('card-1', 'Sommerlager startet', 'cat-news', [], CardStatus::Published, publishedAt: new DateTimeImmutable('2026-07-10 12:00:00', new DateTimeZone('UTC'))),
            new Card('card-2', 'Vereinsmeisterschaft', 'cat-events', [], CardStatus::Draft, position: 2),
        ]);

        $this->assertContains('clubcms-landing-page', $html, 'The landing page wrapper should be rendered.');
        $this->assertContains('clubcms-hero__title', $html, 'The hero section should be rendered.');
        $this->assertContains('clubcms-columns', $html, 'The 4-column section should be rendered.');
        $this->assertContains('News', $html, 'Provided categories should be rendered.');
        $this->assertContains('Termine', $html, 'Provided categories should be rendered.');
        $this->assertContains('Thema 3', $html, 'Missing categories should fall back to placeholders.');
        $this->assertContains('Noch keine Beiträge vorhanden.', $html, 'Empty cards should render a placeholder message.');
        $this->assertContains('Sommerlager startet', $html, 'Cards should be rendered inside their category.');
        $this->assertContains('Vereinsmeisterschaft', $html, 'Second card should be rendered.');
    }

    private function itAddsEditorControlsForLoggedInUsers(): void
    {
        $renderer = new LandingPageRenderer();
        $html = $renderer->render([], [], true);

        $this->assertContains('Bearbeitungsaktionen', $html, 'Editor controls should be rendered for logged-in users.');
        $this->assertContains('Neuer Beitrag', $html, 'Editor controls should contain a new-post action.');
        $this->assertContains('Bearbeiten', $html, 'Editor controls should contain an edit action.');
        $this->assertContains('Löschen', $html, 'Editor controls should contain a delete action.');
    }

    private function assertContains(string $needle, string $haystack, string $message): void
    {
        if (! str_contains($haystack, $needle)) {
            throw new RuntimeException($message . PHP_EOL . 'Missing: ' . $needle);
        }
    }
}
