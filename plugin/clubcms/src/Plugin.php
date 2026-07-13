<?php

declare(strict_types=1);

namespace ClubCMS;

use ClubCMS\Admin\Dashboard;
use ClubCMS\Admin\CardSubmissionHandler;
use ClubCMS\Admin\CardsPage;
use ClubCMS\Admin\DiagnosticsPage;
use ClubCMS\Admin\SettingsPage;
use ClubCMS\Admin\SettingsSubmissionHandler;
use ClubCMS\Frontend\EditorShortcode;
use ClubCMS\Frontend\LandingPageShortcode;
use ClubCMS\Infrastructure\EditorSettingsStorage;
use ClubCMS\Infrastructure\OptionStorage;
use ClubCMS\Repository\CardRepository;
use ClubCMS\Repository\CategoryRepository;
use ClubCMS\Repository\FieldDefinitionRepository;
use ClubCMS\Security\AdminBarGuard;
use ClubCMS\Security\AdminAccessGuard;
use ClubCMS\Security\EditorAccessGuard;

final class Plugin
{
    private Dashboard $dashboard;

    private CardsPage $cardsPage;

    private SettingsPage $settingsPage;

    private DiagnosticsPage $diagnosticsPage;

    private LandingPageShortcode $landingPageShortcode;

    private EditorShortcode $editorShortcode;

    private AdminAccessGuard $adminAccessGuard;

    private AdminBarGuard $adminBarGuard;

    public function register(): void
    {
        $storage = new OptionStorage();
        $editorSettingsStorage = new EditorSettingsStorage($storage);
        $categoryRepository = new CategoryRepository($storage);
        $cardRepository = new CardRepository($storage);
        $fieldDefinitionRepository = new FieldDefinitionRepository($storage);
        $submissionHandler = new SettingsSubmissionHandler($categoryRepository, $fieldDefinitionRepository, $editorSettingsStorage);
        $cardSubmissionHandler = new CardSubmissionHandler($cardRepository, $categoryRepository);
        $editorAccessGuard = new EditorAccessGuard();

        $this->dashboard = new Dashboard($categoryRepository, $fieldDefinitionRepository, $editorSettingsStorage);
        $this->cardsPage = new CardsPage($cardRepository, $categoryRepository, $cardSubmissionHandler);
        $this->settingsPage = new SettingsPage($categoryRepository, $fieldDefinitionRepository, $submissionHandler);
        $this->diagnosticsPage = new DiagnosticsPage();
        $this->landingPageShortcode = new LandingPageShortcode($categoryRepository, $cardRepository, editorSettingsStorage: $editorSettingsStorage);
        $this->editorShortcode = new EditorShortcode(
            $categoryRepository,
            $cardRepository,
            $cardSubmissionHandler,
            $editorAccessGuard
        );
        $this->adminAccessGuard = new AdminAccessGuard();
        $this->adminBarGuard = new AdminBarGuard();

        add_action('init', [$this, 'registerTextDomain']);
        add_action('init', [$this->landingPageShortcode, 'register']);
        add_action('init', [$this->editorShortcode, 'register']);
        add_action('init', [$this, 'hideAdminBar']);
        add_action('after_setup_theme', [$this, 'removeAdminBarBump']);
        add_action('wp_head', [$this, 'printAdminBarHideStyles']);
        add_action('admin_init', [$this->adminAccessGuard, 'enforce']);
        add_filter('show_admin_bar', [$this->adminBarGuard, 'filter']);
        add_action('admin_menu', [$this, 'registerAdminMenu']);
    }

    public function registerTextDomain(): void
    {
        load_plugin_textdomain('clubcms', false, dirname(plugin_basename(CLUBCMS_FILE)) . '/languages');
    }

    public function hideAdminBar(): void
    {
        if (! $this->isAdmin() && function_exists('show_admin_bar')) {
            show_admin_bar(false);
        }
    }

    public function removeAdminBarBump(): void
    {
        if (! $this->isAdmin() && function_exists('remove_action')) {
            remove_action('wp_head', '_admin_bar_bump_cb');
        }
    }

    public function printAdminBarHideStyles(): void
    {
        if (! $this->isAdmin()) {
            echo $this->getAdminBarHideStyles();
        }
    }

    public function getAdminBarHideStyles(): string
    {
        return '<style id="clubcms-admin-bar-hide">#wpadminbar{display:none !important;}html{margin-top:0 !important;}body{margin-top:0 !important;padding-top:0 !important;}</style>';
    }

    private function isAdmin(): bool
    {
        return function_exists('current_user_can') && current_user_can('manage_options');
    }

    public function registerAdminMenu(): void
    {
        add_menu_page(
            'ClubCMS',
            'ClubCMS',
            'manage_options',
            'clubcms',
            [$this->dashboard, 'render'],
            'dashicons-screenoptions',
            26
        );

        add_submenu_page(
            'clubcms',
            'Cards',
            'Cards',
            'manage_options',
            'clubcms-cards',
            [$this->cardsPage, 'render']
        );

        add_submenu_page(
            'clubcms',
            'Einstellungen',
            'Einstellungen',
            'manage_options',
            'clubcms-settings',
            [$this->settingsPage, 'renderGeneralSettings']
        );

        add_submenu_page(
            'clubcms',
            'Kategorien',
            'Kategorien',
            'manage_options',
            'clubcms-categories',
            [$this->settingsPage, 'renderCategories']
        );

        add_submenu_page(
            'clubcms',
            'Felddefinitionen',
            'Felddefinitionen',
            'manage_options',
            'clubcms-field-definitions',
            [$this->settingsPage, 'renderFieldDefinitions']
        );

        add_submenu_page(
            'clubcms',
            'Tests',
            'Tests',
            'manage_options',
            'clubcms-tests',
            [$this->diagnosticsPage, 'render']
        );
    }
}
