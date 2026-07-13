<?php

declare(strict_types=1);

namespace ClubCMS\Admin;

use ClubCMS\Tests\TestSuite;

final class DiagnosticsPage
{
    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die('Insufficient permissions.');
        }

        $results = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['clubcms_form'] ?? '') === 'run_tests') {
            check_admin_referer('clubcms_run_tests');
            $this->loadTestFiles();
            $results = (new TestSuite())->run();
        }

        echo '<div class="wrap">';
        echo '<h1>ClubCMS Tests</h1>';
        echo '<p>Hier kannst du die aktuellen Domain-Tests direkt aus dem WordPress-Backend starten.</p>';
        echo $this->renderRunForm();

        if (is_array($results)) {
            echo $this->renderResults($results);
        }

        echo '</div>';
    }

    private function loadTestFiles(): void
    {
        $bootstrap = CLUBCMS_PATH . 'tests/bootstrap.php';
        $suite = CLUBCMS_PATH . 'tests/TestSuite.php';

        if (is_readable($bootstrap)) {
            require_once $bootstrap;
        }

        if (is_readable($suite)) {
            require_once $suite;
        }
    }

    private function renderRunForm(): string
    {
        ob_start();
        ?>
        <form method="post">
            <?php wp_nonce_field('clubcms_run_tests'); ?>
            <input type="hidden" name="clubcms_form" value="run_tests" />
            <?php submit_button('Tests ausführen'); ?>
        </form>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * @param array<int, array{name: string, status: string, message: string}> $results
     */
    private function renderResults(array $results): string
    {
        $passed = 0;
        $failed = 0;

        foreach ($results as $result) {
            if ($result['status'] === 'passed') {
                $passed++;
                continue;
            }

            $failed++;
        }

        ob_start();
        echo '<h2>Ergebnis</h2>';
        echo '<p><strong>Bestanden:</strong> ' . esc_html((string) $passed) . ' | <strong>Fehlgeschlagen:</strong> ' . esc_html((string) $failed) . '</p>';
        echo '<table class="widefat striped">';
        echo '<thead><tr><th>Test</th><th>Status</th><th>Details</th></tr></thead><tbody>';

        foreach ($results as $result) {
            echo '<tr>';
            echo '<td>' . esc_html($result['name']) . '</td>';
            echo '<td>' . esc_html($result['status'] === 'passed' ? 'OK' : 'Fehler') . '</td>';
            echo '<td>' . esc_html($result['message']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        return (string) ob_get_clean();
    }
}
