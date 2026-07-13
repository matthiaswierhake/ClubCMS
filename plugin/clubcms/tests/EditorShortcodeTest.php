<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Admin\CardSubmissionHandler;
use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use ClubCMS\Domain\Category;
use ClubCMS\Domain\Visibility;
use ClubCMS\Frontend\EditorShortcode;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Security\EditorAccessGuard;
use RuntimeException;

final class EditorShortcodeTest
{
    public function run(): void
    {
        $this->itRendersTheEditorForAuthorizedUsers();
        $this->itBlocksUnauthorizedUsers();
        $this->itSavesCardsFromTheFrontendEditor();
        $this->itShowsValidationErrorsForInvalidInput();
        $this->itFallsBackToAnInlineNoticeWhenHeadersAreAlreadySent();
        $this->itRendersTemplateChoicesForNewCards();
        $this->itPrefillsDuplicatedCardsAsNewDrafts();
    }

    private function itRendersTheEditorForAuthorizedUsers(): void
    {
        $shortcode = $this->createShortcode(
            new EditorShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            new EditorShortcodeCardRepository([
                new Card('card-1', 'Sommerlager', 'cat-news', [], CardStatus::Published, Visibility::Public),
            ])
        );

        $previousGet = $_GET;

        try {
            $_GET = [
                'edit_card' => 'card-1',
            ];

            $html = $shortcode->render([
                'back_to' => '/landing-page/',
            ]);
        } finally {
            $_GET = $previousGet;
        }

        $this->assertContains('Redaktions-Editor', $html, 'The editor heading should render.');
        $this->assertContains('Card bearbeiten', $html, 'The form heading should render.');
        $this->assertContains('Sommerlager', $html, 'The existing card should be listed.');
        $this->assertContains('Bearbeiten', $html, 'Edit action should be visible.');
        $this->assertContains('Löschen', $html, 'Delete action should be visible.');
        $this->assertContains('back_to=%2Flanding-page%2F', $html, 'The back link should be preserved in the editor.');
    }

    private function itBlocksUnauthorizedUsers(): void
    {
        $shortcode = new EditorShortcode(
            new EditorShortcodeCategoryRepository(),
            new EditorShortcodeCardRepository(),
            new CardSubmissionHandler(new EditorShortcodeCardRepository()),
            new EditorAccessGuard(static fn (string $capability): bool => false)
        );

        $html = $shortcode->render();

        $this->assertContains('erforderlichen Rechte', $html, 'Unauthorized users should receive an access warning.');
    }

    private function itSavesCardsFromTheFrontendEditor(): void
    {
        $repository = new EditorShortcodeCardRepository();
        $redirectTarget = null;
        $terminated = false;

        $shortcode = new EditorShortcode(
            new EditorShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            $repository,
            new CardSubmissionHandler($repository),
            new EditorAccessGuard(static fn (string $capability): bool => true),
            redirect: static function (string $url) use (&$redirectTarget): void {
                $redirectTarget = $url;
            },
            requestUri: static fn (): string => '/editor/',
            homeUrl: static fn (): string => 'https://example.test',
            terminate: static function () use (&$terminated): void {
                $terminated = true;
            }
        );

        $previousServer = $_SERVER;
        $previousPost = $_POST;
        $previousGet = $_GET;

        try {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST = [
                'clubcms_form' => 'card',
                'clubcms_action' => 'save',
                'id' => 'card-frontend',
                'original_id' => '',
                'back_to' => '/landing-page/',
                'title' => 'Frontend Beitrag',
                'category_id' => 'cat-news',
                'fields_json' => '{"teaser":"Hallo"}',
                'status' => 'published',
                'visibility' => 'public',
                'position' => '2',
                'published_at' => '2026-07-13 10:00:00',
                'is_static' => '1',
            ];
            $_GET = [];

            $shortcode->render();
        } finally {
            $_SERVER = $previousServer;
            $_POST = $previousPost;
            $_GET = $previousGet;
        }

        $this->assertSame('/landing-page/?saved=1', $redirectTarget, 'Successful saves should redirect back to the landing page.');
        $this->assertTrue($terminated, 'Successful saves should terminate after redirect.');
        $this->assertCount(1, $repository->items, 'The card should be stored.');
        $this->assertSame('Frontend Beitrag', $repository->items[0]->title, 'The card title should be stored.');
    }

    private function itShowsValidationErrorsForInvalidInput(): void
    {
        $repository = new EditorShortcodeCardRepository();
        $shortcode = new EditorShortcode(
            new EditorShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            $repository,
            new CardSubmissionHandler($repository),
            new EditorAccessGuard(static fn (string $capability): bool => true)
        );

        $previousServer = $_SERVER;
        $previousPost = $_POST;
        $previousGet = $_GET;

        try {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST = [
                'clubcms_form' => 'card',
                'clubcms_action' => 'save',
                'id' => 'card-invalid',
                'original_id' => '',
                'back_to' => '/landing-page/',
                'title' => 'Ungültig',
                'category_id' => 'cat-news',
                'fields_json' => '{broken',
                'status' => 'published',
                'visibility' => 'public',
                'position' => '1',
                'published_at' => '',
                'is_static' => '',
            ];
            $_GET = [];

            $html = $shortcode->render();
        } finally {
            $_SERVER = $previousServer;
            $_POST = $previousPost;
            $_GET = $previousGet;
        }

        $this->assertContains('Die Card-Felder enthalten ungültiges JSON.', $html, 'Invalid input should show an error message.');
        $this->assertCount(0, $repository->items, 'Invalid input must not be stored.');
    }

    private function itFallsBackToAnInlineNoticeWhenHeadersAreAlreadySent(): void
    {
        $repository = new EditorShortcodeCardRepository();
        $redirectTarget = null;
        $terminated = false;

        $shortcode = new EditorShortcode(
            new EditorShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            $repository,
            new CardSubmissionHandler($repository),
            new EditorAccessGuard(static fn (string $capability): bool => true),
            redirect: static function (string $url) use (&$redirectTarget): void {
                $redirectTarget = $url;
            },
            requestUri: static fn (): string => '/editor/',
            homeUrl: static fn (): string => 'https://example.test',
            terminate: static function () use (&$terminated): void {
                $terminated = true;
            },
            headersSent: static fn (): bool => true
        );

        $previousServer = $_SERVER;
        $previousPost = $_POST;
        $previousGet = $_GET;

        try {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_POST = [
                'clubcms_form' => 'card',
                'clubcms_action' => 'save',
                'id' => 'card-inline',
                'original_id' => '',
                'back_to' => '/landing-page/',
                'title' => 'Inline',
                'category_id' => 'cat-news',
                'fields_json' => '{"teaser":"Hallo"}',
                'status' => 'published',
                'visibility' => 'public',
                'position' => '1',
                'published_at' => '',
                'is_static' => '',
            ];
            $_GET = [];

            $html = $shortcode->render();
        } finally {
            $_SERVER = $previousServer;
            $_POST = $previousPost;
            $_GET = $previousGet;
        }

        $this->assertSame(null, $redirectTarget, 'No redirect should be attempted once headers are already sent.');
        $this->assertFalse($terminated, 'No terminate call should happen without redirect.');
        $this->assertContains('Card wurde gespeichert.', $html, 'A success notice should be shown inline.');
    }

    private function itRendersTemplateChoicesForNewCards(): void
    {
        $shortcode = $this->createShortcode(
            new EditorShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            new EditorShortcodeCardRepository()
        );

        $previousGet = $_GET;

        try {
            $_GET = [
                'template' => 'event',
            ];

            $html = $shortcode->render();
        } finally {
            $_GET = $previousGet;
        }

        $this->assertContains('Vorlage für neue Beiträge', $html, 'Template chooser should be rendered.');
        $this->assertContains('template=news', $html, 'Template links should be rendered.');
        $this->assertContains('location', $html, 'Template fields should prefill the editor form.');
        $this->assertContains('value="members"', $html, 'Template should preselect the visibility.');
    }

    private function itPrefillsDuplicatedCardsAsNewDrafts(): void
    {
        $shortcode = $this->createShortcode(
            new EditorShortcodeCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            new EditorShortcodeCardRepository([
                new Card('card-1', 'Sommerlager', 'cat-news', ['teaser' => 'Hallo'], CardStatus::Published, Visibility::Public),
            ])
        );

        $previousGet = $_GET;

        try {
            $_GET = [
                'duplicate_card' => 'card-1',
            ];

            $html = $shortcode->render();
        } finally {
            $_GET = $previousGet;
        }

        $this->assertContains('Duplizieren', $html, 'Duplicate action should be rendered.');
        $this->assertContains('card-1-kopie', $html, 'Duplicated cards should get a new id suggestion.');
        $this->assertContains('Sommerlager Kopie', $html, 'Duplicated cards should get a copied title.');
        $this->assertContains('value="draft"', $html, 'Duplicated cards should start as drafts.');
    }

    private function createShortcode(
        CategoryRepositoryInterface $categories,
        CardRepositoryInterface $cards
    ): EditorShortcode {
        return new EditorShortcode(
            $categories,
            $cards,
            new CardSubmissionHandler($cards),
            new EditorAccessGuard(static fn (string $capability): bool => true)
        );
    }

    private function assertContains(string $needle, string $haystack, string $message): void
    {
        if (! str_contains($haystack, $needle)) {
            throw new RuntimeException($message . PHP_EOL . 'Missing: ' . $needle);
        }
    }

    private function assertSame(mixed $expected, mixed $actual, string $message): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true));
        }
    }

    private function assertTrue(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertFalse(bool $condition, string $message): void
    {
        if ($condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertCount(int $expected, array $actual, string $message): void
    {
        if (count($actual) !== $expected) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . $expected . PHP_EOL . 'Actual:   ' . count($actual));
        }
    }
}

final class EditorShortcodeCardRepository implements CardRepositoryInterface
{
    /**
     * @param array<int, Card> $items
     */
    public function __construct(
        public array $items = [],
    ) {
    }

    public function all(): array
    {
        return $this->items;
    }

    public function getById(string $id): ?Card
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function save(Card $card): void
    {
        $this->delete($card->id);
        $this->items[] = $card;
    }

    public function delete(string $id): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            static fn (Card $item): bool => $item->id !== $id
        ));
    }
}

final class EditorShortcodeCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @param array<int, Category> $items
     */
    public function __construct(
        public array $items = [],
    ) {
    }

    public function all(): array
    {
        return $this->items;
    }

    public function getById(string $id): ?Category
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function save(Category $category): void
    {
        $this->items[] = $category;
    }

    public function delete(string $id): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            static fn (Category $item): bool => $item->id !== $id
        ));
    }
}
