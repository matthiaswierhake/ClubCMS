<?php

declare(strict_types=1);

namespace ClubCMS\Rendering;

use ClubCMS\Domain\Card;
use ClubCMS\Domain\Category;

final class LandingPageRenderer
{
    /**
     * @param array<int, Category|null> $categories
     * @param array<int, Card> $cards
     */
    public function render(array $categories, array $cards = [], bool $showEditorControls = false): string
    {
        $columns = $this->buildColumns($categories, $cards);

        ob_start();
        ?>
        <section class="clubcms-landing-page">
            <header class="clubcms-hero">
                <div class="clubcms-hero__content">
                    <p class="clubcms-hero__kicker">ClubCMS</p>
                    <h1 class="clubcms-hero__title">Vereinsinhalte klar organisiert</h1>
                    <p class="clubcms-hero__lead">
                        Landingpage-Grundlage für Themen, Cards und redaktionelle Pflege.
                    </p>
                </div>
            </header>

            <nav class="clubcms-teasers" aria-label="Wichtige Menuepunkte">
                <a class="clubcms-teaser" href="#">Aktuelles</a>
                <a class="clubcms-teaser" href="#">Termine</a>
                <a class="clubcms-teaser" href="#">Verein</a>
                <a class="clubcms-teaser" href="#">Kontakt</a>
            </nav>

            <section class="clubcms-columns" aria-label="ClubCMS Themenbereiche">
                <?php foreach ($columns as $column): ?>
                    <?php echo $this->renderColumnCard($column, $showEditorControls); ?>
                <?php endforeach; ?>
            </section>
        </section>
        <?php

        return (string) ob_get_clean();
    }

    /**
     * @param Category|null $category
     * @param array<int, Card> $cards
     */
    public function renderColumn(?Category $category, array $cards = [], bool $showEditorControls = false): string
    {
        $column = $this->buildColumn($category, $cards, 1);

        ob_start();
        ?>
        <section class="clubcms-columns clubcms-columns--single" aria-label="ClubCMS Themenbereich">
            <?php echo $this->renderColumnCard($column, $showEditorControls); ?>
        </section>
        <?php

        return (string) ob_get_clean();
    }

    /**
     * @param array<int, Category|null> $categories
     * @param array<int, Card> $cards
     * @return array<int, array{title: string, kicker: string, status: string, items: array<int, array{title: string, meta: string}>}>
     */
    private function buildColumns(array $categories, array $cards): array
    {
        $slots = array_slice(array_values($categories), 0, 4);
        $columns = [];

        for ($index = 0; $index < 4; $index++) {
            $columns[] = $this->buildColumn($slots[$index] ?? null, $cards, $index + 1);
        }

        return $columns;
    }

    /**
     * @param array<int, Card> $cards
     * @return array{title: string, kicker: string, status: string, items: array<int, array{title: string, meta: string}>}
     */
    private function buildColumn(?Category $category, array $cards, int $fallbackIndex = 1): array
    {
        $fallback = [
            'title' => 'Thema ' . $fallbackIndex,
            'kicker' => 'Spalte ' . $fallbackIndex,
            'status' => 'Bereich vorbereitet',
        ];

        return [
            'title' => $category?->label ?? $fallback['title'],
            'kicker' => $category?->slug ? strtoupper($category->slug) : $fallback['kicker'],
            'status' => $category === null
                ? $fallback['status']
                : sprintf('Kategorie: %s', $category->sortMode),
            'items' => $this->buildItemsForCategory($category, $cards),
        ];
    }

    /**
     * @param array<int, Card> $cards
     * @return array<int, array{title: string, meta: string}>
     */
    private function buildItemsForCategory(?Category $category, array $cards): array
    {
        if ($category === null) {
            return [];
        }

        $items = array_values(array_filter(
            $cards,
            static fn (Card $card): bool => $card->categoryId === $category->id
        ));

        usort($items, function (Card $left, Card $right) use ($category): int {
            if ($category->sortMode === 'manual') {
                return $left->position <=> $right->position
                    ?: $this->comparePublishedAt($right, $left);
            }

            return $this->comparePublishedAt($right, $left)
                ?: ($left->position <=> $right->position);
        });

        return array_map(
            function (Card $card): array {
                return [
                    'title' => $card->title,
                    'meta' => $this->buildCardMeta($card),
                ];
            },
            $items
        );
    }

    private function buildCardMeta(Card $card): string
    {
        $parts = [strtoupper($card->status->value)];

        if ($card->publishedAt !== null) {
            $parts[] = $card->publishedAt->format('d.m.Y');
        }

        if ($card->isStatic) {
            $parts[] = 'statisch';
        }

        return implode(' · ', $parts);
    }

    private function comparePublishedAt(Card $left, Card $right): int
    {
        $leftTimestamp = $left->publishedAt?->getTimestamp() ?? 0;
        $rightTimestamp = $right->publishedAt?->getTimestamp() ?? 0;

        return $leftTimestamp <=> $rightTimestamp;
    }

    /**
     * @param array{title: string, kicker: string, status: string, items: array<int, array{title: string, meta: string}>} $column
     */
    private function renderColumnCard(array $column, bool $showEditorControls): string
    {
        ob_start();
        ?>
        <article class="clubcms-card">
            <header class="clubcms-card__header">
                <div>
                    <p class="clubcms-card__kicker"><?php echo $this->escapeHtml($column['kicker']); ?></p>
                    <h2 class="clubcms-card__title"><?php echo $this->escapeHtml($column['title']); ?></h2>
                </div>

                <?php if ($showEditorControls): ?>
                    <div class="clubcms-card__actions" aria-label="Bearbeitungsaktionen">
                        <a href="#" aria-label="Neuer Beitrag">＋</a>
                        <a href="#" aria-label="Bearbeiten">✎</a>
                        <a href="#" aria-label="Löschen">🗑</a>
                    </div>
                <?php endif; ?>
            </header>

            <div class="clubcms-card__body">
                <p class="clubcms-card__status"><?php echo $this->escapeHtml($column['status']); ?></p>

                <?php if ($column['items'] !== []): ?>
                    <ul class="clubcms-card__items">
                        <?php foreach ($column['items'] as $item): ?>
                            <li>
                                <strong><?php echo $this->escapeHtml($item['title']); ?></strong>
                                <span><?php echo $this->escapeHtml($item['meta']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="clubcms-card__empty">Noch keine Beiträge vorhanden.</p>
                <?php endif; ?>
            </div>
        </article>
        <?php

        return (string) ob_get_clean();
    }

    private function escapeHtml(string $value): string
    {
        if (function_exists('esc_html')) {
            return (string) esc_html($value);
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
