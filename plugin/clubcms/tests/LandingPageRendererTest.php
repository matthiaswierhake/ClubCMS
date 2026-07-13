<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Domain\Category;
use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use ClubCMS\Rendering\LandingPageRenderer;
use DateTimeImmutable;
use DateTimeZone;
use RuntimeException;

final class LandingPageRendererTest
{
    public function run(): void
    {
        $this->itRendersAFourColumnLandingPage();
        $this->itSortsCardsAccordingToCategoryConfiguration();
        $this->itAddsEditorControlsForLoggedInUsers();
        $this->itUsesConfiguredFrontendEditorUrls();
    }

    private function itRendersAFourColumnLandingPage(): void
    {
        $renderer = new LandingPageRenderer();
        $html = $renderer->render([
            new Category('cat-news', 'News', 'news', 'date_desc'),
            new Category('cat-events', 'Termine', 'termine', 'position_asc'),
        ], [
            new Card('card-1', 'Sommerlager startet', 'cat-news', [], CardStatus::Published, publishedAt: new DateTimeImmutable('2026-07-10 12:00:00', new DateTimeZone('UTC'))),
            new Card('card-2', 'Vereinsmeisterschaft', 'cat-events', [], CardStatus::Draft, position: 2),
        ], true);

        $this->assertContains('clubcms-landing-page', $html, 'The landing page wrapper should be rendered.');
        $this->assertContains('clubcms-hero__title', $html, 'The hero section should be rendered.');
        $this->assertContains('clubcms-columns', $html, 'The 4-column section should be rendered.');
        $this->assertContains('News', $html, 'Provided categories should be rendered.');
        $this->assertContains('Termine', $html, 'Provided categories should be rendered.');
        $this->assertContains('Thema 3', $html, 'Missing categories should fall back to placeholders.');
        $this->assertContains('Noch keine Beiträge vorhanden.', $html, 'Empty cards should render a placeholder message.');
        $this->assertContains('Sommerlager startet', $html, 'Cards should be rendered inside their category.');
        $this->assertContains('Vereinsmeisterschaft', $html, 'Second card should be rendered.');
        $this->assertContains('page=clubcms-cards', $html, 'Frontend actions should link to the Cards admin page.');
        $this->assertContains('edit_card=card-1', $html, 'Edit action should target the matching card.');
        $this->assertContains('category_id=cat-news', $html, 'New-card action should prefill the category.');
    }

    private function itSortsCardsAccordingToCategoryConfiguration(): void
    {
        $renderer = new LandingPageRenderer();

        $dateSortedHtml = $renderer->renderColumn(
            new Category('cat-news', 'News', 'news', 'date_desc'),
            [
                new Card('card-old', 'Älterer Beitrag', 'cat-news', [], CardStatus::Published, publishedAt: new DateTimeImmutable('2026-07-01 12:00:00', new DateTimeZone('UTC'))),
                new Card('card-new', 'Neuer Beitrag', 'cat-news', [], CardStatus::Published, publishedAt: new DateTimeImmutable('2026-07-10 12:00:00', new DateTimeZone('UTC'))),
            ]
        );

        $this->assertOrder('Neuer Beitrag', 'Älterer Beitrag', $dateSortedHtml, 'Date-desc sorting should show newer cards first.');

        $titleSortedHtml = $renderer->renderColumn(
            new Category('cat-events', 'Termine', 'termine', 'title_asc'),
            [
                new Card('card-z', 'Zulu', 'cat-events', [], CardStatus::Published, position: 2),
                new Card('card-a', 'Alpha', 'cat-events', [], CardStatus::Draft, position: 1),
            ]
        );

        $this->assertOrder('Alpha', 'Zulu', $titleSortedHtml, 'Title-asc sorting should show alphabetically ascending cards first.');
    }

    private function itAddsEditorControlsForLoggedInUsers(): void
    {
        $renderer = new LandingPageRenderer();
        $html = $renderer->render([
            new Category('cat-news', 'News', 'news', 'date_desc'),
        ], [
            new Card('card-1', 'Sommerlager startet', 'cat-news', [], CardStatus::Published, publishedAt: new DateTimeImmutable('2026-07-10 12:00:00', new DateTimeZone('UTC'))),
        ], true);

        $this->assertContains('Bearbeitungsaktionen', $html, 'Editor controls should be rendered for logged-in users.');
        $this->assertContains('Neuer Beitrag', $html, 'Editor controls should contain a new-post action.');
        $this->assertContains('Bearbeiten', $html, 'Editor controls should contain an edit action.');
        $this->assertContains('Löschen', $html, 'Editor controls should contain a delete action.');
    }

    private function itUsesConfiguredFrontendEditorUrls(): void
    {
        $renderer = new LandingPageRenderer();
        $html = $renderer->render([
            new Category('cat-news', 'News', 'news', 'date_desc'),
        ], [
            new Card('card-1', 'Sommerlager startet', 'cat-news', [], CardStatus::Published, publishedAt: new DateTimeImmutable('2026-07-10 12:00:00', new DateTimeZone('UTC'))),
        ], true, 'https://example.test/editor/');

        $this->assertContains('https://example.test/editor/?category_id=cat-news', $html, 'New-card action should point to the frontend editor.');
        $this->assertContains('https://example.test/editor/?edit_card=card-1', $html, 'Edit action should point to the frontend editor.');
        $this->assertContains('action="https://example.test/editor/"', $html, 'Delete action should post to the frontend editor.');
    }

    private function assertContains(string $needle, string $haystack, string $message): void
    {
        if (! str_contains($haystack, $needle)) {
            throw new RuntimeException($message . PHP_EOL . 'Missing: ' . $needle);
        }
    }

    private function assertOrder(string $first, string $second, string $haystack, string $message): void
    {
        $firstPosition = strpos($haystack, $first);
        $secondPosition = strpos($haystack, $second);

        if ($firstPosition === false || $secondPosition === false || $firstPosition >= $secondPosition) {
            throw new RuntimeException($message . PHP_EOL . 'First: ' . $first . PHP_EOL . 'Second: ' . $second);
        }
    }
}
