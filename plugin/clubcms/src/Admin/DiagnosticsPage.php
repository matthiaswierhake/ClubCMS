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
        $suite = new TestSuite();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['clubcms_form'] ?? '') === 'run_tests') {
            $testClass = $this->normalizeTestClass((string) ($_POST['clubcms_test'] ?? ''));

            check_admin_referer('clubcms_run_tests');
            $this->loadTestFiles();
            $results = $suite->run($testClass);
        }

        echo '<div class="wrap">';
        echo '<h1>ClubCMS Tests</h1>';
        echo '<p>Hier kannst du die aktuellen Domain-Tests direkt aus dem WordPress-Backend starten.</p>';
        echo $this->renderRunAllForm();
        echo $this->renderIndividualForms($suite->availableTests());

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

    /**
     * @param array<int, array{class: class-string, label: string}> $tests
     */
    private function renderIndividualForms(array $tests): string
    {
        ob_start();
        ?>
        <h2>Einzeltests</h2>
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Test</th>
                <th>Aktion</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tests as $test): ?>
                <tr>
                    <td><?php echo esc_html($test['label']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('clubcms_run_tests'); ?>
                            <input type="hidden" name="clubcms_form" value="run_tests" />
                            <input type="hidden" name="clubcms_test" value="<?php echo esc_attr($test['class']); ?>" />
                            <?php submit_button('Starten', 'secondary', 'submit', false); ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return (string) ob_get_clean();
    }

    private function renderRunAllForm(): string
    {
        ob_start();
        ?>
        <form method="post">
            <?php wp_nonce_field('clubcms_run_tests'); ?>
            <input type="hidden" name="clubcms_form" value="run_tests" />
            <input type="hidden" name="clubcms_test" value="" />
            <?php submit_button('Alle Tests ausführen'); ?>
        </form>
        <?php
        return (string) ob_get_clean();
    }

    private function normalizeTestClass(string $testClass): ?string
    {
        if ($testClass === '') {
            return null;
        }

        $allowed = array_map(
            static fn (array $test): string => $test['class'],
            (new TestSuite())->availableTests()
        );

        return in_array($testClass, $allowed, true) ? $testClass : null;
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
