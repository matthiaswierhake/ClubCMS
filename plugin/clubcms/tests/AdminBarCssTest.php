<?php

declare(strict_types=1);

namespace ClubCMS\Tests;

use ClubCMS\Plugin;
use RuntimeException;

final class AdminBarCssTest
{
    public function run(): void
    {
        $plugin = new Plugin();
        $css = $plugin->getAdminBarHideStyles();

        $this->assertContains('#wpadminbar{display:none !important;}', $css, 'Admin bar CSS should hide the bar.');
        $this->assertContains('html{margin-top:0 !important;}', $css, 'Admin bar CSS should remove top margin.');
        $this->assertContains('body{margin-top:0 !important;padding-top:0 !important;}', $css, 'Admin bar CSS should remove top padding.');
    }

    private function assertContains(string $needle, string $haystack, string $message): void
    {
        if (! str_contains($haystack, $needle)) {
            throw new RuntimeException($message . PHP_EOL . 'Missing: ' . $needle);
        }
    }
}
