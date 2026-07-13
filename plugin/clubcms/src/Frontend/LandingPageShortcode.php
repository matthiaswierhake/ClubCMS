<?php

declare(strict_types=1);

namespace ClubCMS\Frontend;

use ClubCMS\Repository\CategoryRepositoryInterface;
use ClubCMS\Repository\CardRepositoryInterface;
use ClubCMS\Rendering\LandingPageRenderer;

final class LandingPageShortcode
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly CardRepositoryInterface $cardRepository,
        private readonly LandingPageRenderer $renderer = new LandingPageRenderer(),
    ) {
    }

    public function register(): void
    {
        add_shortcode('clubcms_landing_page', [$this, 'render']);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function render(array $attributes = []): string
    {
        $showEditorControls = function_exists('is_user_logged_in') && is_user_logged_in();

        return $this->renderer->render(
            $this->categoryRepository->all(),
            $this->cardRepository->all(),
            $showEditorControls
        );
    }
}
