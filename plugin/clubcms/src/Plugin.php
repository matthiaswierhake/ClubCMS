<?php

declare(strict_types=1);

namespace ClubCMS;

use ClubCMS\Admin\Dashboard;
use ClubCMS\Admin\CardSubmissionHandler;
use ClubCMS\Admin\CardsPage;
use ClubCMS\Admin\DiagnosticsPage;
use ClubCMS\Admin\SettingsPage;
use ClubCMS\Admin\SettingsSubmissionHandler;
use ClubCMS\Frontend\LandingPageShortcode;
use ClubCMS\Infrastructure\OptionStorage;
use ClubCMS\Repository\CardRepository;
use ClubCMS\Repository\CategoryRepository;
use ClubCMS\Repository\FieldDefinitionRepository;
use ClubCMS\Security\AdminBarGuard;
use ClubCMS\Security\AdminAccessGuard;

final class Plugin
{
    private Dashboard $dashboard;

    private CardsPage $cardsPage;

    private SettingsPage $settingsPage;

    private DiagnosticsPage $diagnosticsPage;

    private LandingPageShortcode $landingPageShortcode;

    private AdminAccessGuard $adminAccessGuard;

    private AdminBarGuard $adminBarGuard;

    public function register(): void
    {
        $storage = new OptionStorage();
        $categoryRepository = new CategoryRepository($storage);
        $cardRepository = new CardRepository($storage);
        $fieldDefinitionRepository = new FieldDefinitionRepository($storage);
        $submissionHandler = new SettingsSubmissionHandler($categoryRepository, $fieldDefinitionRepository);
        $cardSubmissionHandler = new CardSubmissionHandler($cardRepository);

        $this->dashboard = new Dashboard($categoryRepository, $fieldDefinitionRepository);
        $this->cardsPage = new CardsPage($cardRepository, $categoryRepository, $cardSubmissionHandler);
        $this->settingsPage = new SettingsPage($categoryRepository, $fieldDefinitionRepository, $submissionHandler);
        $this->diagnosticsPage = new DiagnosticsPage();
        $this->landingPageShortcode = new LandingPageShortcode($categoryRepository, $cardRepository);
        $this->adminAccessGuard = new AdminAccessGuard();
        $this->adminBarGuard = new AdminBarGuard();

        add_action('init', [$this, 'registerTextDomain']);
        add_action('init', [$this->landingPageShortcode, 'register']);
        add_action('admin_init', [$this->adminAccessGuard, 'enforce']);
        add_filter('show_admin_bar', [$this->adminBarGuard, 'filter']);
        add_action('admin_menu', [$this, 'registerAdminMenu']);
    }

    public function registerTextDomain(): void
    {
        load_plugin_textdomain('clubcms', false, dirname(plugin_basename(CLUBCMS_FILE)) . '/languages');
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
