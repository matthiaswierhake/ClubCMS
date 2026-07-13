<?php
/**
 * Plugin Name: ClubCMS
 * Description: Structured content management for WordPress based on a generic card engine.
 * Version: 0.4.0
 * Requires PHP: 8.2
 * Author: ClubCMS
 * Text Domain: clubcms
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('CLUBCMS_VERSION', '0.4.0');
define('CLUBCMS_FILE', __FILE__);
define('CLUBCMS_PATH', plugin_dir_path(__FILE__));
define('CLUBCMS_URL', plugin_dir_url(__FILE__));

$autoload = CLUBCMS_PATH . 'vendor/autoload.php';

if (is_readable($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'ClubCMS\\';

        if (! str_starts_with($class, $prefix)) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = CLUBCMS_PATH . 'src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_readable($file)) {
            require_once $file;
        }
    });
}

add_action('plugins_loaded', static function (): void {
    $plugin = new ClubCMS\Plugin();
    $plugin->register();
});
