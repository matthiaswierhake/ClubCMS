<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Admin\CardSubmissionHandler;
use ClubCMS\Admin\CardsPage;
use ClubCMS\Domain\Card;
use ClubCMS\Domain\CardStatus;
use ClubCMS\Domain\Category;
use ClubCMS\Domain\Visibility;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Repository\CategoryRepositoryInterface;
use RuntimeException;

final class CardsPageTest
{
    public function run(): void
    {
        $this->itRendersTheCardAdministrationPage();
        $this->itSavesCardsThroughTheAdminPage();
        $this->itDeletesCardsThroughTheAdminPage();
    }

    private function itRendersTheCardAdministrationPage(): void
    {
        $page = new CardsPage(
            new InMemoryCardsPageCardRepository([
                new Card('card-1', 'Sommerlager', 'cat-news', [], CardStatus::Published, Visibility::Public),
            ]),
            new InMemoryCardsPageCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            new CardSubmissionHandler(new InMemoryCardsPageCardRepository())
        );

        $html = $this->captureOutput(static function () use ($page): void {
            $page->render();
        });

        $this->assertContains('Cards', $html, 'The cards page heading should render.');
        $this->assertContains('Sommerlager', $html, 'Existing cards should be listed.');
        $this->assertContains('Bearbeiten', $html, 'Edit action should be visible.');
        $this->assertContains('Löschen', $html, 'Delete action should be visible.');
    }

    private function itSavesCardsThroughTheAdminPage(): void
    {
        $repository = new InMemoryCardsPageCardRepository();
        $redirectTarget = null;
        $terminated = false;

        $page = new CardsPage(
            $repository,
            new InMemoryCardsPageCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            new CardSubmissionHandler($repository),
            redirect: static function (string $url) use (&$redirectTarget): void {
                $redirectTarget = $url;
            },
            headersSent: static fn (): bool => false,
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
                'id' => 'card-admin',
                'original_id' => '',
                'title' => 'Admin Beitrag',
                'category_id' => 'cat-news',
                'fields_json' => '{"teaser":"Hallo"}',
                'status' => 'published',
                'visibility' => 'public',
                'position' => '2',
                'published_at' => '2026-07-13 10:00:00',
                'is_static' => '1',
            ];
            $_GET = [];

            $page->render();
        } finally {
            $_SERVER = $previousServer;
            $_POST = $previousPost;
            $_GET = $previousGet;
        }

        $this->assertSame('/admin.php?page=clubcms-cards&saved=1', $redirectTarget, 'Saving through the admin page should redirect to the cards page.');
        $this->assertTrue($terminated, 'Saving through the admin page should terminate after redirect.');
        $this->assertCount(1, $repository->items, 'The card should be stored through the admin page.');
        $this->assertSame('Admin Beitrag', $repository->items[0]->title, 'The stored card should keep the submitted title.');
    }

    private function itDeletesCardsThroughTheAdminPage(): void
    {
        $repository = new InMemoryCardsPageCardRepository([
            new Card('card-delete', 'Zu loeschende Card', 'cat-news'),
        ]);
        $redirectTarget = null;
        $terminated = false;

        $page = new CardsPage(
            $repository,
            new InMemoryCardsPageCategoryRepository([
                new Category('cat-news', 'News', 'news', 'date'),
            ]),
            new CardSubmissionHandler($repository),
            redirect: static function (string $url) use (&$redirectTarget): void {
                $redirectTarget = $url;
            },
            headersSent: static fn (): bool => false,
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
                'clubcms_action' => 'delete',
                'id' => 'card-delete',
            ];
            $_GET = [];

            $page->render();
        } finally {
            $_SERVER = $previousServer;
            $_POST = $previousPost;
            $_GET = $previousGet;
        }

        $this->assertSame('/admin.php?page=clubcms-cards&deleted=1', $redirectTarget, 'Deleting through the admin page should redirect to the cards page.');
        $this->assertTrue($terminated, 'Deleting through the admin page should terminate after redirect.');
        $this->assertCount(0, $repository->items, 'The card should be deleted through the admin page.');
    }

    private function captureOutput(callable $callback): string
    {
        ob_start();
        $callback();

        return (string) ob_get_clean();
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

    private function assertCount(int $expected, array $actual, string $message): void
    {
        if (count($actual) !== $expected) {
            throw new RuntimeException($message . PHP_EOL . 'Expected: ' . $expected . PHP_EOL . 'Actual:   ' . count($actual));
        }
    }
}

final class InMemoryCardsPageCardRepository implements CardRepositoryInterface
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

final class InMemoryCardsPageCategoryRepository implements CategoryRepositoryInterface
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
        $this->delete($category->id);
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
